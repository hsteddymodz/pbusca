<meta charset="utf-8">

<style> div{ font-size:24px; font-family:"Arial"; }</style>

<div><img src="../../img/logo.png" height="150" alt="">
<h3>Resultado da Pesquisa</h3>

<?php

if(!$_SESSION)
    @session_start();

include('../class/Conexao.class.php');

function show_info($titulo, $info){

    if($info != 'SEM INFORMAÇÃO' && $info != '' && $info != ' |  |  | ' && $info != 'INVALIDO' && strlen(trim($info)) > 0)
        return "➜ <b> $titulo</b> $info<br>";

}

$valor = json_decode($_GET['str']);
/*
echo "<pre>";
var_dump($valor);
echo "</pre>";

function getNumbers($str){

            $ret = '';
            for($k = 0; $k < strlen($str); $k++){
                if(is_numeric($str[$k]))
                    $ret .= $str[$k];
            }
            return $ret;

}


*/

if($valor){

    echo show_info("Nome:", $valor->nome);
    echo show_info("Nome da mãe:", $valor->nomeMae);
    echo show_info("Nome do pai:", $valor->nomePai);
    echo show_info("Sexo:", $valor->sexoDescricao);
    echo show_info("Data de Nascimento:", $valor->dataNascimento);

    //echo "➜ <b> Data de Óbito:</b> $dataObito);
    echo show_info("País:", $valor->paisNascimento);
    echo show_info("Município de Nascimento:", $valor->municipioNascimento);
    echo show_info("Cpf:", $valor->cpf);
    //echo "➜ <b>TIPO SANGÜINEO:", $sangue);
    //echo "➜ <b>CNS:", $cns);
    //echo "➜ <b>Raça:", $raca);
    echo show_info("RG:", "$rg | $org | $rguf | $rgdata");

    //echo show_info("País:", $pais);
    echo show_info("Município de Residência:", $valor->enderecoMunicipio );
    echo show_info("Número da Casa:", $valor->enderecoNumero );
    echo show_info("Endereço:", $valor->enderecoLogradouro);
    echo show_info("Bairro:", $valor->enderecoBairro );
    echo show_info("Cep:", $valor->enderecoCep);
    echo show_info("Telefone:", $valor->telefone[0]->ddd." ".$valor->telefone[0]->numero);
    echo '</div>';

}else{
    echo 'error';
}
 ?>

</div>