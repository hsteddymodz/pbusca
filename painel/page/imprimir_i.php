<meta charset="utf-8">

<?php



function show_info($titulo, $info){



    if($info != 'SEM INFORMAÇÃO' && $info != '' && $info != ' |  |  | ' && $info != 'INVALIDO' && strlen(trim($info)) > 0)

        return "➜ <b> $titulo</b> $info<br>";



}





echo '<style> div{ font-size:24px; font-family:"Arial"; }</style>';



echo '<div><img src="https://probusca.com/assets/img/logo.png" height="150" alt="">';



echo '<h3>Resultado da Pesquisa</h3>';



if($_POST['CPF']){ 



    echo show_info("Nome:", $_POST['nome']);

    echo show_info("CPF:", $_POST['CPF']);

    echo show_info("Sexo:", $_POST['sexo']);

    echo show_info("Data de Nascimento:", $_POST['dataNasc']);

    echo show_info("Idade:", $_POST['idade']);

    echo show_info("Signo:", $_POST['signo']);

    echo show_info("Nome da Mãe:", $_POST['nomeMae']);





    if(is_array($_POST['telefones'])){



        foreach($_POST['telefones'] as $tel)

            echo show_info("Telefone:", $tel);



    }



    if(is_array($_POST['enderecos'])){



        foreach($_POST['enderecos'] as $tel)

            echo show_info("Endereço:", $tel);



    }



    if(is_array($_POST['email'])){



        foreach($_POST['email'] as $tel)

            echo show_info("E-mail:", $tel);



    }



    echo '</div>';



}



if($_POST['CNPJ']){



    echo show_info("CNPJ:", $_POST['CNPJ']);

    echo show_info("Razão Social:", $_POST['razaoSocial']);

    echo show_info("Nome Fantasia:", $_POST['nomeFantasia']);

    echo show_info("Data de Abertura:", $_POST['dataAbertura']);

    echo show_info("CNAE:", $_POST['CNAE']);

    echo show_info("Natureza Jurídica:", $_POST['naturezaJuridica']);



    if(is_array($_POST['telefones'])){



        foreach($_POST['telefones'] as $tel)

            echo show_info("Telefone:", $tel);



    }



    if(is_array($_POST['enderecos'])){



        foreach($_POST['enderecos'] as $tel)

            echo show_info("Endereço:", $tel);



    }



    echo '</div>';



}



?>



</div>

</div>

</div>







</body>



</html>

