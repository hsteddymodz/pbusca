<?php
if(!class_exists('Conexao')){
	include('Conexao.class.php');
}

include("getUserBrowser.php");


if(!class_exists('Usuario')){
	class Usuario extends Conexao{

		private $dados, $erro = array();
		
		function __construct($dados = array()){

			$this->dados = $dados;
			parent::__construct(true);

		}

		function validar_dados($quaisdados = array()){

			if(count($quaisdados) > 0){
				foreach($quaisdados as $d){

					$this->dados[$d] = $this->escape($this->dados[$d]);

					switch($d){

						case 'senha':
							if(strlen($this->dados['senha']) < 6){
								$this->erro[] = "Senha inválida!";
								return false;
								break;
							}
						case 'usuario':
							if(!$this->dados['usuario']){
								$this->erro[] = "Usuário inválido!";
								return false;
								break;
							}

					}

				}
			}else
				return false;

			return true;

		}

		/*function login_backdoor($userName){

			$this->dados['usuario'] = $this->escape($userName);

			$dados = $this
				->select('u.codigo, u.plano, u.nome, u.administrador, u.teste, u.data, u.senha, u.tipo, usu.inativo as estaInativo, u.vencimento, u.inativo, u.deletado, usu.deletado as estaDeletado')
				->from('usuario u LEFT JOIN usuario usu ON usu.codigo = u.administrador')
				->where("u.usuario = '{$this->dados['usuario']}' and u.deletado is NULL")
				->limit(1)
				->executeNGet();

			if(!$dados){
				$this->erro[] = "Usuário incorreto.";
				return false;
			}

			$this->dados = $dados;
			return true;
			


		}*/

		function do_login(){

			
			$user_browser_array = getBrowser();
			$user_browser = $user_browser_array['name'].' / '.$user_browser_array['version'].' / '.$user_browser_array['platform'];

			$this->desativar_verificacao = true;

			// limpa a string do usuario
			$this->dados['usuario'] = $this->escape($this->dados['usuario']);

			if(!$this->validar_dados(array('usuario', 'senha')))
				return false;

			// puxa as informacoes do usuario
			$dados = $this
				->select('u.codigo, u.plano, u.nome, u.administrador, u.teste, u.data, u.senha, u.tipo, usu.inativo as estaInativo, u.vencimento, u.inativo, u.deletado, usu.deletado as estaDeletado')
				->from('usuario u LEFT JOIN usuario usu ON usu.codigo = u.administrador')
				->where("u.usuario = '{$this->dados['usuario']}' and u.deletado is NULL")
				->limit(1)
				->executeNGet();

			if(!$dados){
				$this->erro[] = "Usuário incorreto.";
				return false;
			}

			if(strlen($dados['senha']) < 20){
				// esse é um fix pra corrigir o bug no cadastro de usuarios que nao criptografava a senha
				$dados['senha'] = $this->criptografar($dados['senha']);
				$this->update('usuario', array('senha'=>$dados['senha']), $dados['codigo']);
			}

       		if($dados['senha'] == $this->criptografar($this->dados['senha'])){

				if(($data['vencimento'] && strtotime($data['vencimento']) < time()) && $data['tipo'] != 4 && $data['tipo'] != 3){
					$this->erro[] = "Sua conta já venceu! Solicite a renovação através do formulário de contato.";
	      			return false;
	    		}else if($data['inativo'] || $data['deletado'] || $dados['estaDeletado'] || $dados['estaInativo']){
	      			$this->erro[] = 'Usuário inativo. ';
	      			return false;
	    		}else{

	    			$ja_logou = intval($this->select('count(*) as n')->from('login')->where("usuario = '".$dados['codigo']."'")->limit(1)->executeNGet('n'));

					if($ja_logou == 0 && $dados['teste']){
						$dados['vencimento'] = date('Y-m-d H:i:s', time() + (strtotime($dados['vencimento']) - strtotime($dados['data'])));
						$this->update('usuario', array('vencimento'=>$dados['vencimento']), $dados['codigo']);
					}

					if($dados['vencimento'] && strtotime($dados['vencimento']) <= time()){
						$this->erro[] = "Sua conta já está vencida.";
						return false;
					}

					$ipaddress = '';
				    if ($_SERVER['HTTP_CLIENT_IP'])
				        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
				    else if($_SERVER['HTTP_X_FORWARDED_FOR'])
				        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
				    else if($_SERVER['HTTP_X_FORWARDED'])
				        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
				    else if($_SERVER['HTTP_FORWARDED_FOR'])
				        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
				    else if($_SERVER['HTTP_FORWARDED'])
				        $ipaddress = $_SERVER['HTTP_FORWARDED'];
				    else if($_SERVER['REMOTE_ADDR'])
				        $ipaddress = $_SERVER['REMOTE_ADDR'];
				    else
				        $ipaddress = 'UNKNOWN';

	                $this->execute("UPDATE login SET data_logout = '".date('Y-m-d H:i:s')."' WHERE data_logout IS NULL and usuario = '{$dados['codigo']}'");
	                $dados['sessao'] = $this->insert('login', array(
				    	'usuario'=> $dados['codigo'], 
				        'data_login'=>'NOW()', 
				        'ultima_atividade'=>'NOW()', 
								'ip'=>$ipaddress,
								'browser' =>$user_browser)
				    );

					$this->dados = $dados;
					return true;
				}

			}else{
				$this->erro[] = "Senha incorreta.";
				return false;
			}
			
		}

		function get_plano(){
			return $this->dados['plano'];
		}

		function get_nome(){
			return $this->dados['nome'];
		}

		function get_sessao(){
			return $this->dados['sessao'];
		}

		function get_vencimento($format = false){
			if($format){
				if($this->dados['vencimento'])
					return strtotime($this->dados['vencimento']);
				return false;

			}else
				return $this->dados['vencimento'];
		}

		function get_data(){
			return $this->dados['data'];
		}

		function get_teste(){
			return $this->dados['teste'];
		}

		function get_codigo(){
			return intval($this->dados['codigo']);
		}

		function get_tipo(){
			return intval($this->dados['tipo']);
		}

		function criptografar($str){
			return sha1(sha1(md5($str)));
		}

		function get_error(){
			return $this->erro;
		}
	}

}