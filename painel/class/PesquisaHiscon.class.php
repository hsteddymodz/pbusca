<?php

include('Conexao.class.php');
include('LimitarConsulta.function.php');
include('RegistrarConsulta.php');
include('simple_html_dom.php');

// Validações de segurança
$con = new Conexao();
if(!$_SESSION) @session_start();

$usr = $_SESSION['usuario'];
if(!$usr || $usr <= 0){
    echo json_encode('{"success":"false", "response":"USERLOGGEDOUT"}', 1);
    exit();
}
// Fim das validações de segurança 

class PesquisaHiscon {
    
    private $url; 
    private $key;
    private $resultado;
    protected $apiUrl;

    function __construct($numeroBeneficio, $tipo = 2) {
        $this->cleanFolder('hisconPdf/');
        include('get_config_info.function.php');

        $ini_file = get_config_info();
        $this->url = $ini_file['hiscon2']['link'];
        $this->key = $ini_file['hiscon2']['chave'];

        $this->apiUrl = $this->url.'/?KEY='.$this->key.'&SERVICO=HISCON&NB='.$numeroBeneficio.'&CONSULTAR_HISCON=' . $tipo;

        $plataforma = 'hiscon';
        if($tipo != 2)
            $plataforma = 'hisconscpf';
        

        // Inicia o request
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);

        if($_GET['teste']) {
            var_dump($response);
            die();
        }

        if(strpos($response, 'ACESSO NEGADO A ESTE') !== false || strpos($response, 'OCORREU UM ERRO DURANTE O PROCESSAMENTO') !== false) {
            echo '<script>alert("Ocorreu um erro durante o processamento, tente novamente em breve")</script>';
            exit();
        }
        if($tipo != 3) {
            $uniqeid = uniqid(); 
            $filename = 'hisconPdf/extrato'.$uniqeid.'.pdf';
            file_put_contents($filename, $response);
        }

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            if(strrpos($response, 'SEM DADOS PARA O') !== false || strrpos($response, 'Número do benefício inválido') !== false || strrpos($response, 'TENTE NOVAMENTE DENTRE ALGUNS MINUTOS') !== false

                || strrpos($response, 'FAVOR ENTRAR EM CONTATO PARA') !== false

        ){

                if(strrpos($response, 'TENTE NOVAMENTE DENTRE ALGUNS MINUTOS') !== false)
                    die('unavailable');

                if($tipo == 2)
                    die('{"path":"", "response":"FALSE"}');
                else
                    die('Não disponível no momento');
            }

            $html = new simple_html_dom();
            $html->load($response);

            foreach($html->find('div[id=menu]') as $item) {
                $item->outertext = '';
            }

            $response = $html->save();

            //$response = str_replace('<div id="menu">', '', $response);
            //$response = str_replace('<ul id="udm">', '', $response);
            


            $con = new Conexao();
            registrarConsulta($con, $_SESSION['usuario'], $plataforma);
            if($tipo == 2)
                echo '{"path":"'.$filename.'", "response":"OK"}';
            else
                echo $response;

            /*if($_GET['teste'])
                var_dump($response);*/

            die();

        }
    }

    // Funcao para limpar o diretorio de arquivos para arquivos mais velhos que 10 minutos
    function cleanFolder($folderName) {
        if (file_exists($folderName)) {
            foreach (new DirectoryIterator($folderName) as $fileInfo) {
                if ($fileInfo->isDot()) {
                    continue;
                }
                if ($fileInfo->isFile() && time() - $fileInfo->getCTime() >= 600) {
                    unlink($fileInfo->getRealPath());
                }
            }
        }
    }
    
}

if($_GET['teste'])
    new PesquisaHiscon('94425841115', 1);

if (isset($_POST['numero_beneficio']) && !empty($_POST['numero_beneficio'])) {
    $nb = $_POST['numero_beneficio'];

    if($_POST['semCpf'])
        $plataforma = 'hisconscpf';
    else
        $plataforma = 'hiscon';
    
    if(limitarConsulta(null, $_SESSION['usuario'], $plataforma, 1) <= 0){
        echo '<script>alert("Créditos Insuficientes!");</script>';
        exit();
    } else {
        if($_POST['semCpf'])
            $pesquisa = new PesquisaHiscon($nb, 1);
        else
            $pesquisa = new PesquisaHiscon($nb);
    }
}


?>