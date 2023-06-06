<?php
date_default_timezone_set ( 'America/Sao_Paulo' );

if(!class_exists('Conexao')){
	class Conexao{
		/*
		
		Esta classe realiza o gerenciamento da conexão com o banco de dados.
		Exemplos de utilização:
		1) SELECT básico
 
		$conexao = new Conexao();
		$carros  = $conexao
					->select('*')
					->from('tabela_carros')
					->where("marca = 'Chevrolet' and ano = '2009'")
					->executeNGet();
		// A utilização acima retornará um array com todos os carros com marca Chevrolet e ano 2009
		esta biblioteca permite ainda uma coleta mais direta de dados do banco. Supondo que eu queira selecionar o nome de um usuário específico:
		$conexao  = new Conexao();
		$usu_nome = $conexao
					->select('nome')
					->from('usuario')
					->where('codigo = 9')
					->limit(1)
					->executeNGet('nome'); // Especificar a coluna que desejo extrair retorna exatamente o valor presente nela
		2) INSERT Básico
	
		
		$conexao = new Conexao();
		$conexao->insert('usuario', array('nome'=>'Wallace', 'email'=>'wallace@gmail.com', 'data_cadastro'=>'NOW()')); // Retorna True em caso de sucesso
		
		3) UPDATE BASICO
		// Supondo que eu queira atualizar as informacoes nome e email do Wallace
		$conexao = new Conexao();
		$conexao->update('usuario', array('nome'=>'Novo Wallace', 'email'=>'novo_email@wallace.com'));
		*/

		public $mysqli;
		public $query, $insert_id, $select, $from, $where, $orderby, $limit_q, $groupby;
		private $limit;



		function __construct($desativar_verificacao = false){
			
			if(!$_SESSION) @session_start();

			// Esta função realiza a conexão com o banco de dados.
			// Exemplo de utilização: $conexao = new Conexao();
			$this->insert_id = 0;
			$this->limit = 0;

			if($desativar_verificacao == false){

				if(!isset($_SESSION['usuario']) || intval($_SESSION['usuario']) == 0){
					die("<script>
						alert('Você não está logado!'); 
						location.href='/index.php';
					</script>");
				}
			}

			// amazon
			// probusca
			$config = array();
			/*$config['username'] = 'usuario_painel';
			$config['host']     = 'probusca.cuokrxvonlce.sa-east-1.rds.amazonaws.com';
			$config['password'] = 'Y5mM6SbPRO';
			$config['dbname']   = 'painel_probusca';*/

			/*$config['username'] = 'probusca';
			$config['host']     = 'ip-198-58-99-73.cloudezapp.io';
			$config['password'] = '@#S3u1mp0rt4d0';
			$config['dbname']   = 'probusca';*/

			$config['username'] = 'probusca';
			$config['host']     = 'pbserver.cuokrxvonlce.sa-east-1.rds.amazonaws.com';
			$config['password'] = 'LoNtqr52hQdx';
			$config['dbname']   = 'probusca';


			//error_reporting(0); 

			$m = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname'], 3306);
			if ($m->connect_errno) {
				die("Failed to connect to MySQL:" . $m->connect_error);
			}

			$this->mysqli = $m;
		}

		function escape($t){
			// Esta função prepara um valor em texto para a inserção no BD (evitando SQL Injectione e outros hacks)
			// Exemplo de utlilização para
			// $variavel = $conexao->escape("Tentativa de SQL 'injection ");	
			// a $variavel vai ter o valor Tentativa de SQL \' injection, o que evitará uma tentativa de injecao de SQL
			return $this->mysqli->escape_string($t);
		}
		function prepararVariavel($var){
			// utiliza a funcao escape para preparar um array de variáveis para serem inseridas no Banco de Dados
			if(is_array($var)){
				foreach($var as $k=>$v){
					$var[$k] = $this->escape($v);
				}
			}else{
				$var = $this->escape($var);
			}
		}
		function update($tabela, $arr, $codigo, $coluna_codigo = 'codigo', $col_invalida = array()){
			/*
			Esta função realiza o update de informações no banco de dados
			$tabela : nome da tabela que será atualizada
			$arr  : array de dados onde a chave é a o nome da coluna e o conteúdo é o dado a ser atualizado
			$codigo : valor da chave primaria que será atualizado. Por exemplo, atualizando o usuário com codigo 5, $codigo = 5;
			$coluna_codigo : nome da coluna que armazena o código do usuario a ser atualizado, por exemplo: usuario_codigo
			$col_invalida : se alguma das chaves do array $arr conter uma variavel invalida, é só especificar aqui.
			
			// Supondo que eu queira atualizar as informacoes nome e email do Wallace
			$conexao = new Conexao();
			$conexao->update('usuario', array('nome'=>'Novo Wallace', 'email'=>'novo_email@wallace.com'));
			*/
			$m = $this->mysqli;
			$code = "";
			foreach($arr as $col=>$val){
				if(!in_array($col, $col_invalida) && $col != $coluna_codigo)
					$code .= " $col = '".$m->escape_string($val)."', ";
			}
			$code = substr($code, 0, -2);
			$sql_code = "update $tabela set $code where $coluna_codigo = '$codigo'";
			$result = $m->query($sql_code) or die($m->error);
			
			return $result;
		}
		function select($oq){
			/*
			Prepara para a realização de um SELECT. A melhor forma de entender essa função é seguir o exemplo no topo do documento
			*/
			$this->select = " select ".$oq;
			$this->from ="";
			$this->where ="";
			$this->groupby ="";
			$this->limit = "";
			$this->orderby ="";
			$this->limit_q = "";
			return $this;
		}
		function from($from){
			$this->from = " from ".$from;
			return $this;
		}
		function where($where){
			$this->where = " where ".$where;
			return $this;
		}
		function orderby($o){
			$this->orderby = " order by ".$o;
			return $this;
		}
		function groupby($o){
			$this->groupby = " group by ".$o;
			return $this;
		}
		function limit($limit){
			$this->limit = $limit;
			$this->limit_q = " limit ".$limit;
			return $this;
		}
		function delete($tbl, $coluna, $valor){
			// Esta função está desativada por motivos de segurança.
			// É melhor utilizar $conexao->execute('QUERY DE DELEçÂO');
			//return $this->execute("delete from $tbl where $coluna = ''");
		}
		function getQuery(){
			// Retorna a query atual em formato de string.
			// É boa para a execucao de debuggs
			return $this->select.$this->from.$this->where.$this->groupby.$this->orderby.$this->limit_q;
		}
		function execute($qr = false){
			// Executa uma query $qr ou a query atualmente construida usando as funcoes acima
			$this->query = $this->select.$this->from.$this->where.$this->groupby.$this->orderby.$this->limit_q;
			if(!$qr)
				$retorno = 	$this
						->mysqli
						->query($this->query) 
						or die($this->mysqli->error);
			else
				$retorno = 	$this
						->mysqli
						->query($qr) 
						or die($this->mysqli->error);
			$this->insert_id = $this->mysqli->insert_id;
			return $retorno;
		}
		function getCodigo(){
			/* Retorna a ID unica da ultima insercao realizada no BD, por exemplo:
			$conexao->insert('carro', array('marca'=>'chevrolet'));
			$car_codigo = $conexao->getCodigo();
			*/
			return $this->insert_id;
		}
		function executeNGet($coluna = NULL){
			/*
			o funcionamento desta funcao esta exemplificado no topo. É mais fácil olhar o exemplo do que explicá-la.
			Ela basicamente executa um comando SQL já preparado.
			*/
			$this->query = $this->select.$this->from.$this->where.$this->groupby.$this->orderby.$this->limit_q;
			
			$tmp = $this->mysqli->query($this->query) or die($this->mysqli->error);
			if($coluna == NULL){
				if($this->limit == 1)
					return $tmp->fetch_assoc();
				else{
					$final = array();
					while($dado = $tmp->fetch_assoc()){
						$final[] = $dado;
					}
					return $final;
				}
			}else{
				$final = $tmp->fetch_assoc();
				return $final[$coluna];
			}
		}
		function count($tbl, $wh = false){
			// Realiza a contagem de registros de uma determinada tabela MySQL
			// Exemplo:
			// $conexao->count('tabela_carros', "where marca = 'chevrolet'");
			// ou $conexao->count('tabela_carros');
			if(!$wh)
				return $this->select('count(*) as R')->from($tbl)->executeNGet('R');
			else
				return $this->select('count(*) as R')->from($tbl)->where($wh)->executeNGet('R');
		}
		function insert($tabela, $arr, $col_invalida = array()){

			/*
			$tabela : tabela onde os dados estão sendo inseridos
			$arr    : array onde a chave é o nome da coluna e o conteúdo é o valor a ser inserido nesta coluna
			$col_invalida : array de chaves da variavel $arr que nao devem ser consideradas
			
			Realiza a inserção de dados no banco de dados. Exemplo de utilização:
			$conexao = new Conexao();
			$conexao->insert('usuario', array('nome'=>'Wallace', 'email'=>'wallace@gmail.com', 'data_cadastro'=>'NOW()')); // Retorna True em caso de sucesso
			*/
			$code = "";
			$cols = "";
			date_default_timezone_set ( 'America/Sao_Paulo' );

			foreach($arr as $col=>$val){
				if(!in_array($col, $col_invalida) && !is_array($val)){

					//if($val == 'NOW()') echo "$col tem valor $val";

					if(strcmp($val, 'NOW()') == 0)
						$code .= "'".date('Y-m-d H:i:s', time() - 10800)."', ";
					else
						$code .= "'".$this->escape($val)."', ";

					$cols .= $this->escape($col).", ";
				}
			}

			$cols = substr($cols, 0, -2);
			$code = substr($code, 0, -2);
			$codigo_sql = "insert into $tabela ($cols) values($code)";
			
			if($this->execute($codigo_sql))
				return $this->insert_id;
			else
				return -1;
			
		}
		function close() {
			$this->mysqli->close();
		}
	}
}
?>