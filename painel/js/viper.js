/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


    function calcula_port(nb,idx)
    {
        var coef = '';
        var campopz = $('#port_prazo'+nb+idx).val();
        var vlparcela = $('#vlparcela'+nb+idx).text();
        var saldo = $('#saldo'+nb+idx).text();
        var port_af = 0;
        var port_saldo = 0;
        var idxbanco = $('#port_banco'+nb+idx).val();
        if (idxbanco !== '999') 
        {
            vlparcela = toFloat(vlparcela);
            saldo = toFloat(saldo);
            coef=getCoef(idxbanco,campopz);
            port_af = vlparcela / parseFloat(coef);
            port_saldo = port_af - saldo;
            if (port_saldo > 0){
                $('#port_af'+nb+idx).text(moeda(port_af,2,',','.'));
                $('#port_saldo'+nb+idx).text(moeda(port_saldo,2,',','.'));
                $('#saldohd'+nb+idx).val(port_saldo);
            } else {
                $('#port_saldo'+nb+idx).text('NEGATIVO');
                $('#saldohd'+nb+idx).val(0);
            }
        } else {
            $('#port_af'+nb+idx).text('');
            $('#port_saldo'+nb+idx).text('');
            $('#saldohd'+nb+idx).val(0);
        }    
        soma_saldo(nb);
       // alert(vlparcela);
        //impressÃ£o
        $("#imp_port_banco"+nb+idx).text($("#port_banco"+nb+idx+" option:selected").text());
        $("#imp_port_prazo"+nb+idx).text($("#port_prazo"+nb+idx+" option:selected").text());
        $("#imp_port_af"+nb+idx).text($('#port_af'+nb+idx).text());
        $("#imp_port_saldo"+nb+idx).text($('#port_saldo'+nb+idx).text());
    }

    function getCoef(idxbanco,campopz)
    {
        var coef ='';
        var data = {};
        data.itens = jQuery.parseJSON($("#coeficientes").val());
        switch(campopz) {
            case '18':
                coef = data.itens[idxbanco].pz18;
                break;
            case '24':
                coef = data.itens[idxbanco].pz24;
                break;
            case '36':
                coef = data.itens[idxbanco].pz36;
                break;
            case '48':
                coef = data.itens[idxbanco].pz48;
                break;
            case '60':
                coef = data.itens[idxbanco].pz60;
                break;
            case '72':
                coef = data.itens[idxbanco].pz72;
                break;
            default: coef = 99999;
        }
        return coef;
    }
    function soma_saldo(nb)
    {
        var soma = 0;
        $(".saldohd"+nb).each(function() {
            if(!isNaN(this.value) && this.value.length!=0) {
                soma += parseFloat(this.value);
            }
        });
        //$('#cxtotal'+nb).removeClass("hide");
        soma+=parseFloat($("#disp_novo"+nb).val())
        $("#total_com_margem"+nb).text('R$ '+moeda(soma,2,',','.'));
        //Impressao:
        $("#imp_total_lib"+nb).text('R$ '+moeda(soma,2,',','.'));

    }
    function simulamrg(nb,mrg) {
        var maismargem = 0.00;  
        var saldo = parseFloat($("#saldo_mais_margem"+nb).val());
        if (mrg === undefined) {
          mrg = toFloat($("#vlmargem"+nb).val());
          maismargem = parseFloat($("#parc_mais_margem"+nb).val());
        }
        var resultado = 0.00;
        var coef = 0.00;
        var campopz = $('#port_prazo_mg'+nb).val();
        var idxbanco = $('#port_banco_mg'+nb).val();
        if (idxbanco !== '999') 
        {
          coef = getCoef(idxbanco,campopz);
          coef = parseFloat(coef);
          if (!isNaN(coef)) {
              resultado = (parseFloat(mrg)+maismargem) / coef;
              resultado -= parseFloat(saldo);
              if (resultado >= 0){
                  $("#vl_liberado"+nb).val(moeda(resultado,2,',','.'));
                  $('#disp_novo'+nb).val(resultado);

              } else {  
                  $("#disp_novo"+nb).val(0);
                  $("#vl_liberado"+nb).val('NEGATIVO');
              }
             // soma_saldo(nb);
          }
         } else { 
             $("#disp_novo"+nb).val(0);
             $("#vl_liberado"+nb).val(0);
         }
          //impressao:
          $("#imp_port_banco_mg"+nb).text(' | Banco: '+$("#port_banco_mg"+nb+" option:selected").text());
          $("#imp_port_prazo_mg"+nb).text(' | Prazo: '+$("#port_prazo_mg"+nb+" option:selected").text()+'X');
          $("#imp_novo_lib"+nb).text('R$ '+$('#vl_liberado'+nb).val());
          soma_saldo(nb);
    }    
    function simula_inverso(nb,liberado) {
        var parcela = 0;
        var txtparcela = $("#parcela_mg"+nb).val();
        if (txtparcela !== undefined) {
            txtparcela = txtparcela.split(":");
            parcela = parseFloat(txtparcela[1]);
        }      
        var resultado = 0.00;
        var coef = 0.00;
        var campopz = $('#port_prazo_mg'+nb).val();
        var idxbanco = $('#port_banco_mg'+nb).val();
        if (idxbanco !== '999') 
        {
          coef = getCoef(idxbanco,campopz);
          coef = parseFloat(coef);
          if (!isNaN(coef)) {
              resultado = (toFloat(liberado) * coef) - parcela;
              if (resultado > 0){
                  $("#vlmargem"+nb).val(moeda(resultado,2,',','.'));
                  $('#disp_novo'+nb).val(liberado);

              } else {    
                  $("#disp_novo"+nb).val(0);
                  $("#vlmargem"+nb).val(moeda(parseFloat($("#vlmargem"+nb).attr('max')),2,',','.'));
              }
             // soma_saldo(nb);
          }
         } else { 
             $("#disp_novo"+nb).val(0);
             $("#vl_liberado"+nb).val('');
         }   
       soma_saldo(nb);
    }    
    function mais_margem(nb)
    {
        var parcela = 0;
        var saldo = 0;
        var txtparcela = $("#parcela_mg"+nb).val();
        if (txtparcela !== undefined) {
            txtparcela = txtparcela.split(":");
            parcela = parseFloat(txtparcela[1]);
            saldo = parseFloat(txtparcela[2]);
            $("#parc_mais_margem"+nb).val(parcela);
            $("#saldo_mais_margem"+nb).val(saldo);
            disable_parcela(nb,txtparcela[0]);
        }
        var margem = toFloat($("#vlmargem"+nb).val());
        if(isNaN(margem)) margem = 0;
        //$("#vl_liberado"+nb).val(margem+parcela);
        margem+=parcela;
        $("#refinmaismargem"+nb).val(moeda(margem,2,',','.'));
        simulamrg(nb,margem);
    }
    function validaMargem(obj,nb)
    {
        var valor = obj.value;
        if (!/^([0-9])*[,]?[0-9]*$/.test(valor)){
            $("#"+obj.id+"msg").text(' InvÃ¡lido');
        } else { 
          if (toFloat(valor) > parseFloat(obj.max)){
              $("#"+obj.id+"msg").text(' Maior que margem');   
          } else {
            $("#"+obj.id+"msg").text('');
         }    
       }
       mais_margem(nb);
    }
    function validaLiberado(obj,nb)
    {
        var valor = obj.value;
        if (!/^([0-9])*[,]?[0-9]*$/.test(valor)){
            $("#"+obj.id+"msg").text(' InvÃ¡lido');
        } else { 
          $("#"+obj.id+"msg").text('');
       }
      simula_inverso(nb,valor);
    }
    function disable_parcela(nb,idx)
    {
        //ativa
        /*$(".port_controle"+nb).each(function() {
            this.removeClass('hide');
        });*/
        $(".port_controle"+nb).removeClass('hide');
        
        $("#port_banco"+nb+idx).addClass('hide');
        $("#port_prazo"+nb+idx).addClass('hide');
        $('#port_af'+nb+idx).text('');
        $('#port_saldo'+nb+idx).text('');
        $("#saldohd"+nb+idx).val(0);
        soma_saldo(nb);
        
    }
    function toFloat(valor)
    {
        valor = valor.trim().replace(".","");
        valor = parseFloat(valor.replace(",","."));        
        return valor;
    }
    function moeda(valor, casas, separdor_decimal, separador_milhar){ 

        var valor_total = parseInt(valor * (Math.pow(10,casas)));
        var inteiros =  parseInt(parseInt(valor * (Math.pow(10,casas))) / parseFloat(Math.pow(10,casas)));
        var centavos = parseInt(parseInt(valor * (Math.pow(10,casas))) % parseFloat(Math.pow(10,casas)));


        if(centavos%10 == 0 && centavos+"".length<2 ){
         centavos = centavos+"0";
        }else if(centavos<10){
         centavos = "0"+centavos;
        }

        var milhares = parseInt(inteiros/1000);
        inteiros = inteiros % 1000; 

        var retorno = "";

        if(milhares>0){
         retorno = milhares+""+separador_milhar+""+retorno
         if(inteiros == 0){
          inteiros = "000";
         } else if(inteiros < 10){
          inteiros = "00"+inteiros; 
         } else if(inteiros < 100){
          inteiros = "0"+inteiros; 
         }
        }
         retorno += inteiros+""+separdor_decimal+""+centavos;


        return retorno;

    }