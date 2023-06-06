<?php

class teste {

function __construct($cns) {
      $cookieObj = file_get_contents('http://18.228.24.168/cadsus/sessionCookie.txt');
      $cookieJson =  json_decode($cookieObj, true);

      foreach ($cookieJson as $field ) {
        $match = array();
        preg_match('/^OAMRequestContext_cadastro.saude.gov.br/', $field['name'], $match);
        if (count($match)){
          $this->OAMRequestContext = new StdClass();
          $this->OAMRequestContext->name = $field['name'];
          $this->OAMRequestContext->value = $field['value'];
        } else if ($field['name'] == 'JSESSIONID') {
          $this->JSESSIONID = new StdClass();
          $this->JSESSIONID->name = $field['name'];
          $this->JSESSIONID->value = $field['value'];
        } else if ($field['name'] == 'TS014aa60d') {
          $this->TS014aa60d = new StdClass();
          $this->TS014aa60d->name = $field['name'];
          $this->TS014aa60d->value = $field['value'];
        } else if ($field['name'] == 'BIGipServercadastro.saude.gov.br') {
          $this->BIGipServercadastro = new StdClass();
          $this->BIGipServercadastro->name = $field['name'];
          $this->BIGipServercadastro->value = $field['value'];
        } else if ($field['name'] == 'OAM_REQ_COUNT') {
          $this->OAM_REQ_COUNT = new StdClass();
          $this->OAM_REQ_COUNT->name = $field['name'];
          $this->OAM_REQ_COUNT->value = $field['value'];
        } else if ($field['name'] == 'OAM_REQ_0') {
          $this->OAM_REQ_0 = new StdClass();
          $this->OAM_REQ_0->name = $field['name'];
          $this->OAM_REQ_0->value = $field['value'];
        } else if ($field['name'] == '_ga') {
          $this->ga = new StdClass();
          $this->ga->name = $field['name'];
          $this->ga->value = $field['value'];
        } else if ($field['name'] == 'ORA_OTD_JROUTE') {
          $this->ORA_OTD_JROUTE = new StdClass();
          $this->ORA_OTD_JROUTE->name = $field['name'];
          $this->ORA_OTD_JROUTE->value = $field['value'];
        } else if ($field['name'] == '_gid') {
          $this->gid = new StdClass();
          $this->gid->name = $field['name'];
          $this->gid->value = $field['value'];
        } else if ($field['name'] == '_gat_gtag_UA_118904362_1') {
          $this->gat = new StdClass();
          $this->gat->name = $field['name'];
          $this->gat->value = $field['value'];
        } else if ($field['name'] == 'OAM_ID') {
          $this->OAMID = new StdClass();
          $this->OAMID->name = $field['name'];
          $this->OAMID->value = $field['value'];
        } else if ($field['name'] == 'OAMAuthnCookie_cadastro.saude.gov.br:80') {
          $this->OAMAuthnCookie = new StdClass();
          $this->OAMAuthnCookie->name = $field['name'];
          $this->OAMAuthnCookie->value = $field['value'];
        } else if ($field['name'] == 'NO_MAQUINA'){
          $this->NO_MAQUINA = new StdClass();
          $this->NO_MAQUINA->name = $field['name'];
          $this->NO_MAQUINA->value = $field['value'];
        } else if ($field['name'] == 'CNS') {
          $this->CNS = new StdClass();
          $this->CNS->name = $field['name'];
          $this->CNS->value = $field['value'];
        } else if ($field['name'] == 'CO_MAQUINA') {
          $this->CO_MAQUINA = new StdClass();
          $this->CO_MAQUINA->name = $field['name'];
          $this->CO_MAQUINA->value = $field['value'];
        }
      }

      $cookie = $this->ga->name.'='.$this->ga->value.'; '.
                      $this->BIGipServercadastro->name.'='.$this->BIGipServercadastro->value.'; '.
                      $this->OAM_REQ_COUNT->name.'='.$this->OAM_REQ_COUNT->value.'; '.
                      $this->gid->name.'='.$this->gid->value.'; '.
                      $this->CO_MAQUINA->name.'='.$this->CO_MAQUINA->value.'; '.
                      $this->NO_MAQUINA->name.'='.$this->NO_MAQUINA->value.'; '.
                      $this->CNS->name.'='.$this->CNS->value.'; '.
                      $this->TS014aa60d->name.'='.$this->TS014aa60d->value.'; '.
                      $this->OAMRequestContext->name.'='.$this->OAMRequestContext->value.'; '.
                      $this->OAMID->name.'='.$this->OAMID->value.'; '.
                      $this->OAM_REQ_0->name.'='.$this->OAM_REQ_0->value.'; '.
                      $this->OAMAuthnCookie->name.'='.$this->OAMAuthnCookie->value.'; '.
                      $this->JSESSIONID->name.'='.$this->JSESSIONID->value.'; '.
                      $this->ORA_OTD_JROUTE->name.'='.$this->ORA_OTD_JROUTE->value.'; '.
                      $this->gat->name.'='.$this->gat->value; 
    

      $query = 'cns='.$cns;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, 'https://cadastro.saude.gov.br/novocartao/restrito/usuarioConsulta.jsp');
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Host: cadastro.saude.gov.br',
      'User-Agent: Mozilla/5.0 (Linux; Android 7.1.1; Moto E (4) Plus Build/NMA26.42-162) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.91 Mobile Safari/537.36',
      'Cookie: '.$cookie.''
      ));
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_NOBODY, 0);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      $data1 = curl_exec ($ch);  
    
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, 'https://cadastro.saude.gov.br/novocartao/restrito/consultar/visualizar.form');
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Host: cadastro.saude.gov.br',
      'User-Agent: Mozilla/5.0 (Linux; Android 7.1.1; Moto E (4) Plus Build/NMA26.42-162) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.91 Mobile Safari/537.36',
      'Cookie: '.$cookie.''
      ));
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_NOBODY, 0);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
      $content = (curl_exec($ch));
      echo $content;
      $this->content = $content;

      // debug de erro
      if(curl_errno($ch)) die(curl_error($ch));
      curl_close ($ch);

      $this->resultado = $content;
    }
  }

  if ($_GET['teste']) {
    $testing = new teste('701001823773799');
  }