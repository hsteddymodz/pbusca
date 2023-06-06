<?php

/*error_reporting(E_ALL);
ini_set("display_errors",1);*/

include('LimitarConsulta.function.php');
include('RegistrarConsulta.php');

include('simple_html_dom.php');

set_time_limit(60);

// Fim das validações de segurança
class PesquisaPessoalGold {
    private $nomeEmpresa;
    private $resultado;
    private $proxy;

    function __construct(){
        include('get_config_info.function.php');
        $ini_file  = get_config_info();
        
        $credenciais_boa_vista = $ini_file['boavista'];
        $this->nomeEmpresa = $credenciais_boa_vista['nome_empresa_bv']; 

        if(trim($credenciais_boa_vista['proxy']) != '')
            $this->proxy = $credenciais_boa_vista['proxy'];
        else
            $this->proxy = false;

    }
    
    function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }


    function boaVistaCPF($cpf) {
                
        $cpf = preg_replace("/[^0-9]/", "", $cpf);
        
        if (strlen($cpf) != 11) {
            echo '<script>alert("Insira um CPF válido.");</script>';
            exit;
        }

        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL, 'https://web2.bvsnet.com.br/transacional/produtos.php?p=ACERTA_COMPLETO&t=F');
        curl_setopt($ch2, CURLOPT_COOKIEFILE, 'cookie/BoaVistaNodeGenerated.txt');
        curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch2, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch2, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36'); 
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);

        if($this->proxy)
            curl_setopt($ch2, CURLOPT_PROXY, $this->proxy);

        $err = curl_error($ch);
        $string = curl_exec($ch2);
        curl_close($ch2);

        if ($err) {
            echo "cURL Error #:" . $err;
        }   

        $ecs = $this->get_string_between($string, "<input type='hidden' name='ecs' id='ecs' value='", "' />");
        $lu_opera = $this->get_string_between($string, "<input type='hidden' name='lu_opera' id='lu_opera' value='", "' />");
        $lu_codig = $this->get_string_between($string, "<input type='hidden' name='lu_codig' id='lu_codig' value='", "' />");
        $lu_ipnum = $this->get_string_between($string, "<input type='hidden' name='lu_ipnum' id='lu_ipnum' value='", "' />");
        $lk_acess = $this->get_string_between($string, "<input type='hidden' name='lk_acess' id='lk_acess' value='", "' />");
        $lu_codpr = $this->get_string_between($string, "<input type='hidden' name='lu_codpr' id='lu_codpr' value='", "' />");
        $lu_rpent = $this->get_string_between($string, "<input type='hidden' name='lu_rpent' id='lu_rpent' value='", "' />");
        $lu_cod11 = $this->get_string_between($string, "<input type='hidden' name='lu_cod11' id='lu_cod11' value='", "' />");
        $lu_contr = $this->get_string_between($string, "<input type='hidden' name='lu_contr' id='lu_contr' value='", "' />");
        $lu_tkbvt = $this->get_string_between($string, "<input type='hidden' name='lu_tkbvt' id='lu_tkbvt' value='", "' />");
        $origem = $this->get_string_between($string, "<input type='hidden' name='origem' id='origem' value='", " />");
        $lu_cdbvt = $this->get_string_between($string, "<input type='hidden' name='lu_cdbvt' id='lu_cdbvt' value='", "' />");
        $lu_cblck = $this->get_string_between($string, "<input type='hidden' name='lu_cblck' id='lu_cblck' value='", "' />");
        $token = $this->get_string_between($string, "<input type='hidden' name='token' id='token' value='", "' />");
        $ss_uid = $this->get_string_between($string, "<input type='hidden' name='ss_uid' id='ss_uid' value='", "' />");
        $ses_id = $this->get_string_between($string, "<input type='hidden' name='ses_id' id='ses_id' value='", "' />");

        $params2 = http_build_query(array(
            'ecs' => $ecs,
            'lu_opera' => $lu_opera,
            'lu_codig' => $lu_codig,
            'lu_ipnum' => $lu_ipnum,
            'lk_acess' => $lk_acess,
            'lu_codpr' => $lu_codpr,
            'lu_rpent' => $lu_rpent,
            'lu_cod11' => $lu_cod11,
            'lu_contr' => $lu_contr,
            'lu_tkbvt' => $lu_tkbvt,
            'origem' => $origem,
            'lu_cdbvt' => $lu_cdbvt,
            'lu_cblck' => $lu_cblck,
            'token' => $token,
            'nomeRelatorio' => 'Pessoal Gold',
            'ss_uid' => $ss_uid,
            'ses_id' => $ses_id,
            'origem' => $origem
            )
        );

        $ch3 = curl_init();
        curl_setopt($ch3, CURLOPT_URL, 'https://acerta.bvsnet.com.br/FamiliaAcertaPFWeb/formularioAcertaCompleto');
        curl_setopt($ch3, CURLOPT_POST, true);
        curl_setopt($ch3, CURLOPT_COOKIEFILE, 'cookie/BoaVistaNodeGenerated.txt');
        curl_setopt($ch3, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch3, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch3, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch3, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36');
        curl_setopt($ch3, CURLOPT_POSTFIELDS, $params2);    

        if($this->proxy)
            curl_setopt($ch3, CURLOPT_PROXY, $this->proxy);


        curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch3);
        curl_close($ch3);

        $documentos = ",,,,,".$cpf;

        $params3 = http_build_query(array('quantidade' => '',
            'valor' => '0,00',
            'documento' => $cpf,
            'documentos' => ",,,,," . $cpf,
            'cepConfirmacao' => '',
            'opcaoCpf' => 'doc',
            'nomeFormulario' => 'Acerta Completo',
            'consulta' => 'doc',
            'cpf1' => '',
            'cpf2' => '',
            'cpf3' => '',
            'cpf4' => '',
            'cpf5' => '',
            'multiplasPaginas' => 'true',
            'comboScoreResult' => '07',
            'comboScore' => '07',
            /*'comboScore' => '09',
            'comboScore' => '54',
            'comboScore' => '63',*/
            'comboCreditoResult' => '',
            'comboTipoCredito' => 'CD',
            'txtTelefone' => '',
            'txtTelefone' => '',
            'chkCheque' => 'on',
            'chequeSimples' => 'simples',
            'cheque' => 'S',
            'cmc7Mascara1' => '',
            'cmc7Mascara2' => '',
            'cmc7Mascara3' => '',
            'cmc7TotalChequesMascara' => '',
            'cmc7ValorMascara' => '0,00',
            'bancoMascara' => '',
            'agenciaMascara' => '',
            'contaCorrenteMascara' => '',
            'digitoContaMascara' => '',
            'numeroChequeMascara' => '',
            'digitoChequeMascara' => '',
            'totalChequeMascara' => '',
            'dataChequeMascara' => '',
            'valorMascara' => '0,00'));

        /*$params3 = http_build_query(array(
            'quantidade' => '',
            'valor' => '0,00',
            'documento' => $cpf,
            'documentos' => $documentos,
            'cepConfirmacao' => '',
            'opcaoCpf' => 'doc',
            'nomeFormulario' => 'Acerta Completo',
            'consulta' => 'doc',
            'cpf1' => '',
            'cpf2' => '',
            'cpf3' => '',
            'cpf4' => '',
            'cpf5' => '',
            'multiplasPaginas' => 'true',
            'comboScoreResult: 07, 09, 54, 63',
            'comboScoreResult' => '07, ',
            'comboScore' => '07',
            'comboCreditoResult' => '',
            'comboTipoCredito' => 'CD',
            'txtTelefone' => '',
            'txtTelefone' => '',
            'chkCheque' => 'on',
            'chequeSimples' => 'simples',
            'cheque' => 'S',
            'cmc7Mascara1' => '',
            'cmc7Mascara2' => '',
            'cmc7Mascara3' => '',
            'cmc7TotalChequesMascara' => '',
            'cmc7ValorMascara' => '0,00',
            'bancoMascara' => '',
            'agenciaMascara' => '',
            'contaCorrenteMascara' => '',
            'digitoContaMascara' => '',
            'numeroChequeMascara' => '',
            'digitoChequeMascara' => '',
            'totalChequeMascara' => '',
            'dataChequeMascara' => '',
            'valorMascara' => '0,00'
            )
        );*/

        $ch4 = curl_init();
        curl_setopt($ch4, CURLOPT_URL, 'https://acerta.bvsnet.com.br/FamiliaAcertaPFWeb/resultadoConsulta');
        curl_setopt($ch4, CURLOPT_POST, true);
        curl_setopt($ch4, CURLOPT_COOKIEFILE, 'cookie/BoaVistaNodeGenerated.txt');
        curl_setopt($ch4, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch4, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch4, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch4, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36');
        curl_setopt($ch4, CURLOPT_POSTFIELDS, $params3);    
        
        if($this->proxy)
            curl_setopt($ch4, CURLOPT_PROXY, $this->proxy);
        
        curl_setopt($ch4, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch4);
        $error = curl_error($ch4);
        curl_close($ch4);

        if ($error) {
            echo '<script>alert("' . $error . '")</script>';
            exit;
        }

        $html = new simple_html_dom();
        $html->load($response);

        if(!$html->find('body', 0))
            die('<h1>Falha ao pesquisar. Tente novamente por favor.</h1>'); 

        $html = $html->find('body', 0);      

        if($html->find('#all', 0))
            $html->find('#all', 0)->setAttribute('style', "width:1000px;");

        $html->find('body', 0)->innertext = '' . $html->find('body', 0)->innertext;

        if($html->find('.help-back'))
            foreach($html->find('.help-back') as $rem)
                $rem->outertext = '';

        $response = $html;

        $response = preg_replace('!<div\s+class="help-back">.*?</div>!is', '', $response);
        $response = str_replace('resources/images/ico-erro-modal.png', 'https://consumer.bvsnet.com.br/FamiliaAcertaPFWeb/resources/images/ico-erro-modal.png', $response);
        $response = str_replace('resources/images/nao-recomendado.png', 'https://consumer.bvsnet.com.br/FamiliaAcertaPFWeb/resources/images/nao-recomendado.png', $response);
        $response = str_replace('resources/images/recomendado.png', 'https://consumer.bvsnet.com.br/FamiliaAcertaPFWeb/resources/images/recomendado.png', $response);
        $response = str_replace('/FamiliaAcertaPFWeb/resources/img/ico/pdf.gif', 'https://consumer.bvsnet.com.br/FamiliaAcertaPFWeb/resources/img/ico/pdf.gif', $response);
        $response = str_replace('resources/images/score_seta_verde_claro.png', 'https://consumer.bvsnet.com.br/FamiliaAcertaPFWeb/resources/images/score_seta_verde_claro.png', $response);
        $response = str_replace('resources/images/score_seta_verde_escuro.png', 'https://consumer.bvsnet.com.br/FamiliaAcertaPFWeb/resources/images/score_seta_verde_escuro.png', $response);
        $response = str_replace('resources/images/score_seta_vermelho.png', 'https://consumer.bvsnet.com.br/FamiliaAcertaPFWeb/resources/images/score_seta_vermelho.png', $response);
        $response = str_replace('resources/images/score_seta_amarelo.png', 'https://consumer.bvsnet.com.br/FamiliaAcertaPFWeb/resources/images/score_seta_amarelo.png', $response);
        $response = str_replace('resources/images/score_seta_laranja.png', 'https://consumer.bvsnet.com.br/FamiliaAcertaPFWeb/resources/images/score_seta_laranja.png', $response);
        $response = str_replace('resources/img/ico_exc_03.png', 'https://consumer.bvsnet.com.br/FamiliaAcertaPFWeb/resources/img/ico_exc_03.png', $response);
        $response = str_replace('/FamiliaAcertaPFWeb/resources/_img/ico/seta-voltar.gif', 'https://consumer.bvsnet.com.br/FamiliaPessoalWeb/resources/_img/ico/seta-voltar.gif', $response);
        $response = str_replace('/FamiliaAcertaPFWeb/resources/img/reg_sma_bus.png', 'https://consumer.bvsnet.com.br/FamiliaAcertaPFWeb/resources/img/reg_sma_bus.png', $response);
        $response = str_replace('/FamiliaAcertaPFWeb/resources/img/bg_transparente.gif', 'https://consumer.bvsnet.com.br/FamiliaAcertaPFWeb/resources/img/bg_transparente.gif', $response);
        $response = str_replace('/FamiliaAcertaPFWeb/resources/img/set_03.gif', 'https://consumer.bvsnet.com.br/FamiliaAcertaPFWeb/resources/img/set_03.gif', $response);
        $response = str_replace('/FamiliaAcertaPFWeb/formularioAcertaCompleto', '', $response);
        $response = str_replace('/FamiliaAcertaPFWeb/resources/_img/ico/ajuda.gif', 'https://consumer.bvsnet.com.br/FamiliaAcertaPFWeb/resources/_img/ico/ajuda.gif', $response);
        $response = str_replace('/FamiliaAcertaPFWeb/resources/img/bt_nco.gif', 'https://consumer.bvsnet.com.br/FamiliaAcertaPFWeb/resources/img/bt_nco.gif', $response);
        $response = str_replace('/FamiliaAcertaPFWeb/resources/img/ico/imprimir.gif', 'https://consumer.bvsnet.com.br/FamiliaAcertaPFWeb/resources/img/ico/imprimir.gif', $response);
        $response = str_replace('/FamiliaAcertaPFWeb/resources/img/bt_con.gif', 'https://consumer.bvsnet.com.br/FamiliaAcertaPFWeb/resources/img/bt_con.gif', $response);
        $response = str_replace('/FamiliaAcertaPFWeb/resources/img/img_bvs_sco_Score.png', 'https://consumer.bvsnet.com.br/FamiliaAcertaPFWeb/resources/img/img_bvs_sco_Score.png', $response);
        $response = str_replace('resources/images/score_seta_cinza.png', 'https://consumer.bvsnet.com.br/FamiliaAcertaPFWeb/resources/images/score_seta_cinza.png', $response);
        $response = str_replace('Este relatório de informações foi gerado para uso exclusivo e confidencial de .', '', $response);
        $response = str_replace('/FamiliaAcertaPFWeb/relatorio/pessoal/gold?origem=LOGINUNIFICADO', '', $response);
        $response = str_replace('src="/FamiliaAcertaPFWeb/pages/relatorio/carregando.jsp', '', $response);
        $response = str_replace('<iframe id="iframeClickGold" " width="965" height="360" frameborder="0" marginwidth="0" marginheight="0" scrolling="auto"></iframe>', '', $response);
        $response = str_replace('/FamiliaAcertaPFWeb/resources/img/ico_3.png', 'https://consumer.bvsnet.com.br/FamiliaAcertaPFWeb/resources/img/ico_3.png', $response);
        $response = str_replace($this->nomeEmpresa, 'PROBUSCA', $response);

        return '<link href="//probusca.com/assets/css/boavista.css" rel="stylesheet">
        <script src="//probusca.com/assets/js/boavista.js"></script>' . $response;

    }    
}


if(!$_SESSION) @session_start();
if(!isset($_POST['token']) || (isset($_POST['token']) && $_POST['token'] != '52afbbe24b53dec42c9b7d0208dc5d5b28cdfdc99dcdb8b5cfdf06a96ad47ffd1d813020'))
    if(!isset($_SESSION['usuario']))
        die('<h1>Não autorizado</h1>');

if (isset($_POST['cpf']) && (!empty($_POST['cpf']))) {

    ?>
    <style>
    .mensagem-erro {
        color:red;
        font-weight:bold;
    }
    .titulo {
        color:#5DB7FB;
        font-weight:bold;
    }
    #botaoResultado {
        display:none;
    }
    .modal-content {
        position: relative;
        background-color: #fff;
        -webkit-background-clip: padding-box;
        background-clip: padding-box;
        border: 0;
        border: 0;
        border-radius: 0;
        outline: 0;
        max-height: 768px;
        overflow: scroll;
    }
    .alinhaPrintHead {
        margin-right:10%;
    }
    .valor-score  {
        display:none !important;
    }
    .descricao-score{
        display:none !important;
    }
    #msgScoreGold {
        background: #F3F5F6;
        font-size: 18px;
        color: black;
        padding: 10px;
        color: black;
    }
    .tabelaImgScore table,
    .tabelaImgScore th,
    .tabelaImgScore td {
        border:none !important; 
    }
    .cnt-resultado table, 
    .cnt-resultado th, 
    .cnt-resultado td {
        border: 2px solid white;
    }
    </style>

    <?php

    $cpf = $_POST['cpf'];
    if(!isset($_POST['token']) || (isset($_POST['token']) && $_POST['token'] != '52afbbe24b53dec42c9b7d0208dc5d5b28cdfdc99dcdb8b5cfdf06a96ad47ffd1d813020'))
        if(limitarConsulta(null, $_SESSION['usuario'], 'gold', 1) <= 0)
            die('<h1>Créditos insuficientes</h1>');
    
    $pesquisa = new PesquisaPessoalGold();
    $result = $pesquisa->boaVistaCPF($cpf);
    if (preg_match('/O Modelo do Score n&atilde;o foi informado/', $result))
        die($result);

    if(!isset($_POST['token']) || (isset($_POST['token']) && $_POST['token'] != '52afbbe24b53dec42c9b7d0208dc5d5b28cdfdc99dcdb8b5cfdf06a96ad47ffd1d813020')) {
        registrarConsulta(null, $_SESSION['usuario'], 'gold');
        echo '<div class="hidden-print" style="width:100%; padding:15px;"><button onclick="window.print();">Imprimir</button>  <button onclick="window.close();">Fechar</button></div>';
    }
    
    die($result);

}



?>