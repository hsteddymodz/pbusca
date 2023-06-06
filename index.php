<?php

$error = array();

$config = parse_ini_file('painel/class/config.ini');
define("CDN", $config['cdn']);


if(isset($_POST['usuario']) && isset($_POST['senha'])){

  include('painel/class/curl_get_contents.function.php'); 
  // verifica o captcha
  $secretKey    = "6Lf04WMUAAAAAPgiYJmPiIkoInsVJOqwAWygqQYG";
  $ip           = $_SERVER['REMOTE_ADDR'];
  $captcha      = $_POST['g-recaptcha-response'];
  $response     = curl_get_contents("https://www.google.com/recaptcha/api/siteverify?secret="
    .$secretKey
    ."&response="
    .$captcha
    ."&remoteip="
    .$ip);

  $responseKeys = json_decode($response, true);

  $error = array();

  if(intval($responseKeys["success"]) !== 1){
    $error[] = "Captcha inválido!";
  } else {



    include('painel/class/Conexao.class.php');
    include('painel/class/Usuario.class.php');

    $usu   = new Usuario($_POST);

    if($usu->do_login()){

      echo "Redirecionando...";

      // logou!
      session_start();

      $_SESSION['usuario']    = $usu->get_codigo();
      $_SESSION['nome']    = $usu->get_nome();
      $_SESSION['plano']    = $usu->get_plano();
      $_SESSION['tipo']       = $usu->get_tipo();
      $_SESSION['vencimento'] = $usu->get_vencimento(1);
      $_SESSION['lastAction'] = time();
      $_SESSION['sessao']     = $usu->get_sessao();

      die('<script>location.href="/painel";</script>');

    }else{
      unset($_SESSION);
      $error = $usu->get_error();
    }

  }

}

?><!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PROBusca</title>
  <link href="https://probusca.com/assets/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
  <link href="https://probusca.com/assets/css/main.css" rel="stylesheet" id="bootstrap-css">
  <link href="https://probusca.com/assets/css/login-form.css" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
  <script src='https://www.google.com/recaptcha/api.js' async defer></script>
  <script>

	function onSubmit(token) {
		document.getElementById('captcha-response').value = token;
    	document.getElementById("form").submit();
  	}

  </script>


</head>

<body>
       <?php
          if($error && count($error) > 0){
            echo '<div class="alert alert-danger">';
            foreach($error as $e){ echo "<p>$e</p>"; }
            echo '</div>';
          }
          ?>

          <div class="login-page">
            <img class="logo-pro" style="text-align:center" src="/assets/img/logo.png" alt="Logo do probusca">
            <div class="form">
              <form class="login-form" accept-charset="UTF-8" method="post" id="form" role="form">
                <fieldset>

                	<input type="hidden" name="g-recaptcha-response" id="captcha-response" value="">
                  <input type="text" placeholder="Usuário" required name="usuario" type="text"/>
                  <input type="password" placeholder="Senha" required name="senha" type="password" value=""/>

                  <button class="g-recaptcha" type="button" data-sitekey="6Lf04WMUAAAAAHK6sqe4AbnOg0bSFZuO6pE_jSIY" data-callback='onSubmit'>Acessar</button>
                </fieldset>
              </form>
            </div>
        </div>

        <div class="form-group text-center msg-compra">
          <ul style="font-size:18px">
            <li>Para adquirir sua conta, entre com contato com um dos seguintes números:</li>
            <li>Whats: (11) 942522213 (MARIO)</li>
            <li>Whats: +5076225-4167 (MAX)</li>
            <li>Whats: (11) 96411-5286 (JOAO)</li>
            <li>Whats: (11) 94481-7454 (JULIANO)</li>
            <li>Whats: (11) 97408-4269 (LEONARDO LINS)</li>
            <li>Whats: ‭(11) 97505-2876‬ (MAX 2019)</li>
          </ul>
          <div class="form-group text-center">
            <p style="color:red;"><b>Atenção!!! NÃO NOS RESPONSABILIZAMOS POR LOGINS COMPRADOS FORA DO SITE, SOMENTE NOS CONTATOS OFICIAIS.</b></p>
          </div>
        </div>

        <!--<div class="form-group text-center">
          Whats: +5076225-4167 (MAX)<br>
Whats: (11) 94481-7454 (JULIANO)<br>
Whats: (11) 96411-5286 (JOAO)<br>
Whats: (11) 97408-4269 (LEONARDO LINS)


        </div>
        <div class="form-group text-center">
          <p style="color:red;"><b>Atenção!!! NÃO NOS RESPONSABILIZAMOS POR LOGINS COMPRADOS FORA DO SITE, SOMENTE NOS CONTATOS OFICIAIS.</b></p>
        </div>-->


<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/5c31618382491369baa0a899/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->