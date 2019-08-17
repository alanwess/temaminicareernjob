<?php
	
	class CrawlerIndeed {

		//Estrutura de dados das vagas
		private $codigos = array();
		private $titulos = array();
		private $empresas = array();
		private $localidades = array();
		private $salarios = array();
		private $resumos = array();
		private $conteudos = array();
		private $links = array();

		//Estrutura de estados, cidades, areas e funcoes
		public $estados = array();
		public $cidades = array();
		public $areas = array();
		public $funcoes = array();
		
		// Configuração DB
		private $db;
		private $host;
		private $user;
		private $pass;
		
		// Configuração WP API
		private $username;
		private $password;
		private $baseurlwp;
		private $token_now = '';
		private $token_valid = 0;

		public function __construct($db, $host, $user, $pass, $username, $password, $baseurlwp){

			$this->db = $db;
			$this->host = $host;
			$this->user = $user;
			$this->pass = $pass;
			$this->username = $username;
			$this->password = $password;
			$this->baseurlwp = $baseurlwp;

		}

		private function ReplaceSiglas($estado){

			switch ($estado) {
				case 'Acre':
					return 'AC';
					break;
				case 'Alagoas':
					return 'AL';
					break;
				case 'Amazonas':
					return 'AM';
					break;
				case 'Amapá':
					return 'AP';
					break;
				case 'Bahia':
					return 'BA';
					break;
				case 'Ceará':
					return 'CE';
					break;
				case 'Distrito Federal':
					return 'DF';
					break;
				case 'Espírito Santo':
					return 'ES';
					break;
				case 'Goiás':
					return 'GO';
					break;
				case 'Maranhão':
					return 'MA';
					break;
				case 'Mato Grosso':
					return 'MT';
					break;
				case 'Mato Grosso do Sul':
					return 'MS';
					break;
				case 'Minas Gerais':
					return 'MG';
					break;
				case 'Pará':
					return 'PA';
					break;
				case 'Paraíba':
					return 'PB';
					break;
				case 'Paraná':
					return 'PR';
					break;
				case 'Pernambuco':
					return 'PE';
					break;
				case 'Piauí':
					return 'PI';
					break;
				case 'Rio de Janeiro':
					return 'RJ';
					break;
				case 'Rio Grande do Norte':
					return 'RN';
					break;
				case 'Rondônia':
					return 'RO';
					break;
				case 'Rio Grande do Sul':
					return 'RS';
					break;
				case 'Roraima':
					return 'RR';
					break;
				case 'Santa Catarina':
					return 'SC';
					break;
				case 'Sergipe':
					return 'SE';
					break;
				case 'São Paulo':
					return 'SP';
					break;		
				case 'Tocantins':
					return 'TO';
					break;	
			}
		}

		public function getLinksEstado($start_url){

			$options = array('http'=>array('method'=>"GET", 'header'=>"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36\n"));
			$context = stream_context_create($options);

			$doc = new DomDocument();
			@$doc->loadHTML(file_get_contents($start_url, false, $context));
			$finder = new DomXPath($doc);
			$classname="card__jobs__links";
			$elementos  = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
			$links_estados = array();
			$count = 0;
			foreach ($elementos as $node) 
			{
				foreach ($node->childNodes as $el){
					if ($el->nodeName == "a"){
						$this->estados[] = trim(preg_replace('/[0-9]+/', '', str_replace(",", "", str_replace(")", "", str_replace("(", "", str_replace("Vagas de emprego ", "", $el->nodeValue))))));
						printf("Obtido estado ".++$count."...\r\n");
						$this->cidades[] = $this->getLinksCidades('https://www.trabalhabrasil.com.br'.$el->getAttribute("href"), $count);
					}
				}
			}

			array_pop($this->estados);
			array_pop($this->cidades);

		}

		public function getLinksCidades($estado_url, $estado_count){

			$options = array('http'=>array('method'=>"GET", 'header'=>"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36\n"));
			$context = stream_context_create($options);

			$doc = new DomDocument();
			@$doc->loadHTML(file_get_contents($estado_url, false, $context));
			$finder = new DomXPath($doc);
			$classname="card__jobs__links";
			$elementos  = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
			$cidades = array();
			$count = 0;
			foreach ($elementos as $node) 
			{
				foreach ($node->childNodes as $el){
					if ($el->nodeName == "a"){
						$cidades[] = trim(preg_replace('/[0-9]+/', '', str_replace(" Vagas de Emprego em ", "", $el->nodeValue)));
					}
				}
				printf("Obtido cidade ".++$count." do estado ".$estado_count."...\r\n");
				array_pop($cidades);
			}

			return $cidades;

		}

		public function getLinksArea($start_url){

			$options = array('http'=>array('method'=>"GET", 'header'=>"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36\n"));
			$context = stream_context_create($options);

			$doc = new DomDocument();
			@$doc->loadHTML(file_get_contents($start_url, false, $context));
			$finder = new DomXPath($doc);
			$classname="card__jobs__links";
			$elementos  = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
			$count = 0;
			foreach ($elementos as $node) 
			{
				foreach ($node->childNodes as $el){
					if ($el->nodeName == "a"){
						$this->areas[] = trim(preg_replace('/[0-9]+/', '', str_replace(",", "", str_replace(")", "", str_replace("(", "", $el->nodeValue)))));
						printf("Obtido area ".++$count."...\r\n");
						$this->funcoes[] = $this->getLinksFuncao('https://www.trabalhabrasil.com.br'.$el->getAttribute("href"), $count);
					}
				}
			}

		}

		public function getLinksFuncao($area_url, $funcao_count){

			$options = array('http'=>array('method'=>"GET", 'header'=>"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36\n"));
			$context = stream_context_create($options);

			$doc = new DomDocument();
			@$doc->loadHTML(file_get_contents($area_url, false, $context));
			$finder = new DomXPath($doc);
			$classname="card__jobs__links";
			$elementos  = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
			$funcoes = array();
			$count = 0;
			foreach ($elementos as $node) 
			{
				foreach ($node->childNodes as $el){
					if ($el->nodeName == "a"){
						$funcoes[] = trim(preg_replace('/[0-9]+/', '', str_replace(")", "", str_replace("(", "", $el->nodeValue))));
						printf("Obtido funcao ".++$count." da area ".$funcao_count."...\r\n");
					}
				}
			}

			return $funcoes;

		}

		public function exploreWorld(){

			for ($es = 0; $es < sizeof($this->estados); $es++){
				printf("Extraindo cidades do estado: ".$this->estados[$es]."...\r\n");
				for ($ci = 0; $ci < sizeof($this->cidades[$es]); $ci++){
					printf("Definindo cidade: ".$this->cidades[$es][$ci]."...\r\n");
					for ($ar = 0; $ar < sizeof($this->areas); $ar++){
						printf("Extraindo funcões da área: ".$this->areas[$ar]."...\r\n");
						for ($fu = 0; $fu < sizeof($this->funcoes[$ar]); $fu++){
							printf("Pesquisando por funcao '".$this->funcoes[$ar][$fu]."' na cidade '".$this->cidades[$es][$ci]."'...\r\n");
							$this->feedData($this->areas[$ar], $this->funcoes[$ar][$fu], $this->cidades[$es][$ci], $this->estados[$es]);
						}
					}
				}
			}

		}

		public function exploreWorld_estados(){
			
			for ($es = 0; $es < sizeof($this->estados); $es++){
				printf("Definindo estado: ".$this->estados[$es]."...\r\n");
				for ($ar = 0; $ar < sizeof($this->areas); $ar++){
					printf("Extraindo funcões da área: ".$this->areas[$ar]."...\r\n");
					for ($fu = 0; $fu < sizeof($this->funcoes[$ar]); $fu++){
						printf("Pesquisando por funcao '".$this->funcoes[$ar][$fu]."' no estado '".$this->estados[$es]."'...\r\n");
						$this->feedData_estados($this->areas[$ar], $this->funcoes[$ar][$fu], $this->estados[$es]);
					}
				}
			}
		}

		private function feedData($area, $funcao, $cidade, $estado){

			$table = $this->getJobs($funcao, $cidade);

			$this->codigos = array();
			$this->titulos = array();
			$this->empresas = array();
			$this->localidades = array();
			$this->salarios = array();
			$this->resumos = array();
			$this->conteudos = array();
			$this->links = array();

			for ($j = 1; $j < sizeof($table); $j++){
				
				if (trim($table[$j]['link']) != ""){
					printf("Processando vaga (".$j.")...\r\n");
					printf("- Vaga --------------------------------------------------------------\r\n");

					$this->codigos[] = md5(trim($table[$j]['link']));

					if(array_key_exists('titulo', $table[$j])){
						$titulo = $table[$j]['titulo'];
						$titulo = preg_replace('/[0-9]+/', '', $titulo);
						$titulo = str_replace('#', '', $titulo);
						$titulo = ucfirst(mb_strtolower(trim($titulo)));
						$this->titulos[] = $titulo;
						echo("Titulo da vaga: '".$titulo."'\r\n");
					} else {
						$this->titulos[] = '';
					}

					if(array_key_exists('empresa', $table[$j])){
						$this->empresas[] = trim($table[$j]['empresa']);
						echo("Empresa da vaga: '".trim($table[$j]['empresa'])."'\r\n");
					} else {
						$this->empresas[] = '';
					}

					if(array_key_exists('localidade', $table[$j])){
						$this->localidades[] = trim($table[$j]['localidade']);
						echo("Localidade da vaga: '".trim($table[$j]['localidade'])."'\r\n");
					} else {
						$this->localidades[] = '';
					}

					if(array_key_exists('salario', $table[$j])){
						$this->salarios[] = trim($table[$j]['salario']);
						echo("Salario da vaga: '".trim($table[$j]['salario'])."'\r\n");
					} else {
						$this->salarios[] = '';
					}

					if(array_key_exists('resumo', $table[$j])){
						$this->resumos[] = trim($table[$j]['resumo']);
						echo("Resumo da vaga: '".trim($table[$j]['resumo'])."'\r\n");
					} else {
						$this->resumos[] = '';
					}

					if(array_key_exists('link', $table[$j])){
						$this->links[] = trim($table[$j]['link']);
						$conteudo = $this->getContent(trim($table[$j]['link']));
						$this->conteudos[] = $conteudo;
						echo("Link da vaga: '".$table[$j]['link']."'\r\n");
						echo("Conteudo da vaga: '".$conteudo."'\r\n");
					} else {
						$this->links[] = '';
						$this->conteudos[] = '';
					}
					printf("---------------------------------------------------------------------\r\n");
					printf("Vaga adicionada a estrutura de dados...\r\n\r\n");
				}
			}

			if (sizeof($table) > 0){
				printf("---------------------------------------------------------------------\r\n");
				printf("Inserindo vagas no banco de dados para [".$funcao."] em [".$cidade."]...\r\n\r\n");
				$this->setJobs($area, $funcao, $cidade, $estado, $this->exportDataJSON());
				printf("---------------------------------------------------------------------\r\n");
			}
		}

		private function feedData_estados($area, $funcao, $estado){

			$table = $this->getJobs($funcao, $estado);

			$this->codigos = array();
			$this->titulos = array();
			$this->empresas = array();
			$this->localidades = array();
			$this->salarios = array();
			$this->resumos = array();
			$this->conteudos = array();
			$this->links = array();

			for ($j = 1; $j < sizeof($table); $j++){
				
				if (trim($table[$j]['link']) != ""){
					printf("Processando vaga (".$j.")...\r\n");
					printf("- Vaga --------------------------------------------------------------\r\n");

					$this->codigos[] = md5(trim($table[$j]['link']));

					if(array_key_exists('titulo', $table[$j])){
						$titulo = $table[$j]['titulo'];
						$titulo = preg_replace('/[0-9]+/', '', $titulo);
						$titulo = str_replace('#', '', $titulo);
						$titulo = ucfirst(mb_strtolower(trim($titulo)));
						$this->titulos[] = $titulo;
						echo("Titulo da vaga: '".$titulo."'\r\n");
					} else {
						$this->titulos[] = '';
					}

					if(array_key_exists('empresa', $table[$j])){
						$this->empresas[] = trim($table[$j]['empresa']);
						echo("Empresa da vaga: '".trim($table[$j]['empresa'])."'\r\n");
					} else {
						$this->empresas[] = '';
					}

					if(array_key_exists('localidade', $table[$j])){
						$this->localidades[] = trim($table[$j]['localidade']);
						echo("Localidade da vaga: '".trim($table[$j]['localidade'])."'\r\n");
					} else {
						$this->localidades[] = '';
					}

					if(array_key_exists('salario', $table[$j])){
						$this->salarios[] = trim($table[$j]['salario']);
						echo("Salario da vaga: '".trim($table[$j]['salario'])."'\r\n");
					} else {
						$this->salarios[] = '';
					}

					if(array_key_exists('resumo', $table[$j])){
						$this->resumos[] = trim($table[$j]['resumo']);
						echo("Resumo da vaga: '".trim($table[$j]['resumo'])."'\r\n");
					} else {
						$this->resumos[] = '';
					}

					if(array_key_exists('link', $table[$j])){
						$this->links[] = trim($table[$j]['link']);
						$conteudo = $this->getContent(trim($table[$j]['link']));
						$this->conteudos[] = $conteudo;
						echo("Link da vaga: '".$table[$j]['link']."'\r\n");
						echo("Conteudo da vaga: '".$conteudo."'\r\n");
					} else {
						$this->links[] = '';
						$this->conteudos[] = '';
					}
					printf("---------------------------------------------------------------------\r\n");
					printf("Vaga adicionada a estrutura de dados...\r\n\r\n");
				}
			}

			if (sizeof($table) > 0){
				printf("---------------------------------------------------------------------\r\n");
				printf("Inserindo vagas no banco de dados para [".$funcao."] em [".$estado."]...\r\n\r\n");
				$this->setJobs_estados($area, $funcao, $estado, $this->exportDataJSON());
				printf("---------------------------------------------------------------------\r\n");
			}
		}

		private function getJobs($query, $local){

			$query = urlencode($query);
			$local = urlencode($local);

			$url = 'https://www.indeed.com.br/jobs?q='.$query.'&l='.$local;

			$options = array(
				'http'=>array('method'=>"GET", 'header'=>"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36\n")
				);
			$context = stream_context_create($options);

			$doc = new DomDocument();
			@$doc->loadHTML(file_get_contents($url, false, $context));

			$finder = new DomXPath($doc);
			$classname="jobsearch-SerpJobCard";
			$elementos = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");

			$tmp_dom = new DOMDocument(); 
			$result = array();
			foreach ($elementos as $node) 
			{
				$vaga = array();
			    $tmp_node = $tmp_dom->importNode($node,true);
			    foreach ($tmp_node->childNodes as $el){
			    	if ($el->nodeName == "div"){

			    		if($el->getAttribute("class") == "title"){
							foreach ($el->childNodes as $el2){
								if ($el2->nodeName == "a"){
									if ($el2->nodeValue != ""){ 
										$vaga += array("titulo" => $el2->nodeValue);
									}
									$vaga += array("link" => 'http://www.indeed.com.br'.$el2->getAttribute("href")); 
								}
							} 
						}
						if($el->getAttribute("class") == "sjcl"){
							foreach ($el->childNodes as $el2){
								if ($el2->nodeName == "div" && $el2->getAttribute("class") == "location"){
									if ($el2->nodeValue != "") $vaga += array("localidade" => $el2->nodeValue);
								}
								if ($el2->childNodes != NULL){
									foreach ($el2->childNodes as $el3){
										if ($el3->nodeName == "span"){
											if ($el3->nodeValue != "") $vaga += array("empresa" => $el3->nodeValue);
										}
									}
								}
							} 
						}
						if($el->getAttribute("class") == "salarySnippet"){
							foreach ($el->childNodes as $el2){
								if ($el2->nodeName == "span"){
									if ($el2->nodeValue != "") $vaga += array("salario" => $el2->nodeValue);
								}
							} 
						}
						if($el->getAttribute("class") == "summary"){
							if ($el->nodeValue != "") $vaga += array("resumo" => $el->nodeValue); 
						}
					}
			    }
			    $result[] = $vaga;
			}

			return($result);
		}

		private function getContent($url) {
			
			$options = array(
				'http'=>array('method'=>"GET", 'header'=>"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36\n")
				);
			$context = stream_context_create($options);

			$doc = new DomDocument();
			@$doc->loadHTML(file_get_contents($url, false, $context));

			$finder = new DomXPath($doc);
			$classname = "jobsearch-JobComponent-description";
			$elementos = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");

			$tmp_dom = new DOMDocument(); 
			$content = "";
			$control_foreach = 0;
			foreach ($elementos as $node) 
			{
			    $tmp_node = $tmp_dom->importNode($node,true);
			    foreach ($tmp_node->childNodes as $el){
			    	if ($el->nodeName == "div" && $el->getAttribute("id") == "jobDescriptionText"){
			    		foreach ($el->childNodes as $el2){
			    			if ($el2->nodeName == "div"){
			    				$content .= '<p>'.$el2->nodeValue.'</p>';
			    			} else if ($el2->nodeName == "p"){
				    			if ($el2->childNodes != null){
				    				foreach ($el2->childNodes as $el3){
				    					$content .= '<p>'.$el3->nodeValue.' </p>';
				    					if ($el2->nodeName == "br"){
				    						$content .= '<br>';
				    					}
				    				}
				    			} else {
				    				$content .= '<p>'.$el2->nodeValue.' </p>';
				    			}
				    		} else if ($el2->nodeName == "ul"){
				    			foreach ($el2->childNodes as $el3){
				    				$content .= '<ul>'.$el3->nodeValue.'</ul>';
				    			}
				    		} else {
				    			$content .= '<p>'.$el2->nodeValue.'</p>';
				    		}
				    		$control_foreach = 1;
				    	}
				    	if ($control_foreach == 0){
				    		$content .= '<p>'.$el->nodeValue.'</p>';
				    	}
					}
			    }
			}

			return($content);
		}

		public function exportDataJSON(){
			return(
				json_encode(
					array(
						"codigos" 	  => $this->codigos, 
						"titulos"	  => $this->titulos, 
						"empresas" 	  => $this->empresas,
						"resumos"	  => $this->resumos,
						"localidades" => $this->localidades,
						"salarios" 	  => $this->salarios,  
						"conteudos"   => $this->conteudos, 
						"links"		  => $this->links
					)
				)
			);
		}

		public function setJobs($area, $funcao, $cidade, $estado, $consulta){
	
			try {
			    $pdo = new PDO("mysql:dbname=".$this->db.";host=".$this->host, $this->user, $this->pass);
			    $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );//Error Handling
			    $sql = "CREATE TABLE IF NOT EXISTS careernjob_jobsindeed (
			     	Id INT( 11 ) AUTO_INCREMENT PRIMARY KEY,
			     	area TEXT,
			     	funcao TEXT,
			     	cidade TEXT, 
			     	estado TEXT, 
			    	results LONGTEXT, 
			    	query_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci" ;
			    $pdo->exec($sql);
			    $sql = "INSERT INTO careernjob_jobsindeed (area, funcao, cidade, estado, results) VALUES (:area, :funcao, :cidade, :estado, :results)" ;
			    $stmt = $pdo->prepare($sql);    
			    $stmt->bindParam(':area', $area, PDO::PARAM_STR);   
			    $stmt->bindParam(':funcao', $funcao, PDO::PARAM_STR);  
			    $stmt->bindParam(':cidade', $cidade, PDO::PARAM_STR);
			    $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);                           
				$stmt->bindParam(':results', $consulta, PDO::PARAM_STR);                                  
				$stmt->execute(); 

				printf("Realizado a inserção no banco de dados para [".$funcao."] em [".$cidade."]...\r\n\r\n");
			} catch(PDOException $e) {
			    printf($e->getMessage()."\n");
			}

		}

		public function setJobs_estados($area, $funcao, $estado, $consulta){
	
			try {
			    $pdo = new PDO("mysql:dbname=".$this->db.";host=".$this->host, $this->user, $this->pass);
			    $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );//Error Handling
			    $sql = "CREATE TABLE IF NOT EXISTS careernjob_jobsindeed_estados (
			     	Id INT( 11 ) AUTO_INCREMENT PRIMARY KEY,
			     	area TEXT,
			     	funcao TEXT,
			     	estado TEXT, 
			    	results LONGTEXT, 
			    	query_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci" ;
			    $pdo->exec($sql);
			    $sql = "INSERT INTO careernjob_jobsindeed_estados (area, funcao, estado, results) VALUES (:area, :funcao, :estado, :results)" ;
			    $stmt = $pdo->prepare($sql);    
			    $stmt->bindParam(':area', $area, PDO::PARAM_STR);   
			    $stmt->bindParam(':funcao', $funcao, PDO::PARAM_STR);  
			    $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);                           
				$stmt->bindParam(':results', $consulta, PDO::PARAM_STR);                                  
				$stmt->execute(); 

				printf("Realizado a inserção no banco de dados para [".$funcao."] no estado [".$estado."]...\r\n\r\n");
			} catch(PDOException $e) {
			    printf($e->getMessage()."\n");
			}

		}

		public function getJobsDB($date_start, $date_end){
			try {
				$pdo = new PDO("mysql:dbname=".$this->db.";host=".$this->host, $this->user, $this->pass);
			    $stmt = $pdo->query("SELECT area, funcao, cidade, estado, results FROM careernjob_jobsindeed WHERE query_date BETWEEN '".$date_start."' and '".$date_end."'");
			    $global = 1;
				while ($row = $stmt->fetch()) {
				    printf("Inicio da nova linha do banco (Resgatando query [".$row['funcao']."] para cidade [".$row['cidade']."])\r\n");
				    $vagas = json_decode($row['results']);
				    for ($j = 0; $j < sizeof($vagas->codigos); $j++){
				    	if ($vagas->conteudos[$j] == "" ||  $vagas->titulos[$j] == "" || $vagas->links[$j] == ""){
				    		echo("- VAGA --------------------------------------------------------------\r\n");
				    		echo ("Vaga ".$global++." não mostrada por não estar nos padrões...\r\n");
				    		echo("---------------------------------------------------------------------\r\n");
				    		printf("\n\n");
				    	} else {
					    	echo("- VAGA --------------------------------------------------------------\r\n");
					    	echo("Contador: ".$global++."\r\n");
					    	echo("Código: ".$vagas->codigos[$j]."\r\n");
							echo("Titulo: ".$vagas->titulos[$j]."\r\n");
							echo("Empresas: ".$vagas->empresas[$j]."\r\n");
							echo("Resumo: ".$vagas->resumos[$j]."\r\n");
							echo("Localidade: ".$vagas->localidades[$j]."\r\n");
							echo("Salário: ".$vagas->salarios[$j]."\r\n");
							echo("Conteudo: ".$vagas->conteudos[$j]."\r\n");
							echo("Link: ".$vagas->links[$j]."\r\n");
							echo("---------------------------------------------------------------------\r\n");
							printf("\n\n");
						}
					}
				}
			} catch(PDOException $e) {
			    printf($e->getMessage()."\n");
			}
		}

		public function getJobsDB_estados($date_start, $date_end){
			try {
				$pdo = new PDO("mysql:dbname=".$this->db.";host=".$this->host, $this->user, $this->pass);
			    $stmt = $pdo->query("SELECT area, funcao, estado, results FROM careernjob_jobsindeed_estados WHERE query_date BETWEEN '".$date_start."' and '".$date_end."'");
			    $global = 1;
				while ($row = $stmt->fetch()) {
				    printf("Inicio da nova linha do banco (Resgatando query [".$row['funcao']."] para o estado [".$row['estado']."])\r\n");
				    $vagas = json_decode($row['results']);
				    for ($j = 0; $j < sizeof($vagas->codigos); $j++){
				    	if ($vagas->conteudos[$j] == "" ||  $vagas->titulos[$j] == "" || $vagas->links[$j] == ""){
				    		echo("- VAGA --------------------------------------------------------------\r\n");
				    		echo ("Vaga ".$global++." não mostrada por não estar nos padrões...\r\n");
				    		echo("---------------------------------------------------------------------\r\n");
				    		printf("\n\n");
				    	} else {
					    	echo("- VAGA --------------------------------------------------------------\r\n");
					    	echo("Contador: ".$global++."\r\n");
					    	echo("Código: ".$vagas->codigos[$j]."\r\n");
							echo("Titulo: ".$vagas->titulos[$j]."\r\n");
							echo("Empresas: ".$vagas->empresas[$j]."\r\n");
							echo("Resumo: ".$vagas->resumos[$j]."\r\n");
							echo("Localidade: ".$vagas->localidades[$j]."\r\n");
							echo("Salário: ".$vagas->salarios[$j]."\r\n");
							echo("Conteudo: ".$vagas->conteudos[$j]."\r\n");
							echo("Link: ".$vagas->links[$j]."\r\n");
							echo("---------------------------------------------------------------------\r\n");
							printf("\n\n");
						}
					}
				}
			} catch(PDOException $e) {
			    printf($e->getMessage()."\n");
			}
		}

		private function generateToken($username, $password){
			$process = curl_init($this->baseurlwp.'/wp-json/simple-jwt-authentication/v1/token');
			$data = array(
					'username' => $username, 
					'password' => $password 
			);
			$data_string = json_encode($data);
			curl_setopt($process, CURLOPT_TIMEOUT, 30);
			curl_setopt($process, CURLOPT_POST, 1);
			curl_setopt($process, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($process, CURLOPT_POSTFIELDS, $data_string);
			curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($process, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: '.strlen($data_string)));
			$return = curl_exec($process);
			$err = curl_error($process);
			curl_close($process);

			if ($err) {
			  	printf("cURL Error #:" . $err ."\n");
			} else {
				$this->token_now = json_decode($return)->token;
				return($this->token_now);
			}

		}

		private function validateToken($token){
			$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => $this->baseurlwp."/wp-json/simple-jwt-authentication/v1/token/validate",
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 30,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "POST",
			  CURLOPT_HTTPHEADER => array("Authorization: Bearer ".$token, "Cache-control: no-cache")
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
			  	printf("cURL Error #:" . $err ."\n");
			} else {
				if(json_decode($response)->data->status == "403") $this->validateToken($this->generateToken($this->username, $this->password));
				else $this->token_valid = 1;
			}

		}

		private function req_insertJob($codigo, $titulo, $empresa, $resumo, $localidade, $salario, $conteudo, $link, $cidade, $estado, $area, $funcao, $count){

			$this->validateToken($this->token_now);

			if ($this->token_valid == 1){
				$process = curl_init($this->baseurlwp.'/wp-json/api/createjobindeed');
				$data = array(
					'codigo' => $codigo,
					'titulo' => $titulo,
					'empresa' => $empresa,
					'resumo' => $resumo,
					'localidade' => $localidade,
					'salario' => $salario,
					'conteudo' => $conteudo,
					'link' => $link,
					'cidade' => $cidade,
					'estado' => $estado,
					'area' => $area,
					'funcao' => $funcao
				);
				$data_string = json_encode($data);
				curl_setopt($process, CURLOPT_TIMEOUT, 30);
				curl_setopt($process, CURLOPT_POST, 1);
				curl_setopt($process, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($process, CURLOPT_POSTFIELDS, $data_string);
				curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);	
				curl_setopt($process, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json', 'Authorization: Bearer '.$this->token_now, 'Content-Length: '.strlen($data_string)));
				$return = curl_exec($process);
				$err = curl_error($process);
				curl_close($process);

				if ($err) {
				  	printf("cURL Error para vaga (".$count.") #:" . $err ."\n");
				} else {
					printf("Vaga [".$count."](".$titulo."): ".json_decode($return)->message."\n");
				}

			} else {
				printf("Erro ao validar token\n");
				return("error");
			}

		}

		function insertJobs($date_start, $date_end){
			try {
			   $pdo = new PDO("mysql:dbname=".$this->db.";host=".$this->host, $this->user, $this->pass);
			    $stmt = $pdo->query("SELECT area, funcao, cidade, estado, results FROM careernjob_jobsindeed WHERE query_date BETWEEN '".$date_start."' and '".$date_end."'");
			    $global = 0;
			    $codigos_vagas = array();
				while ($row = $stmt->fetch()) {
				    $vagas = json_decode($row['results']);
      				for ($j = 0; $j < sizeof($vagas->codigos); $j++){
      					if ($vagas->conteudos[$j] == "" ||  $vagas->titulos[$j] == "" || $vagas->links[$j] == "")
						{
							echo("Vaga [".++$global."](".$vagas->titulos[$j].") não anexada por não estar nos moldes necessários.\r\n");
						} else {

							if (!in_array($vagas->codigos[$j], $codigos_vagas)){
								
								echo("Anexando vaga [".++$global."](".$vagas->titulos[$j].")...\r\n");
	      						
	      						if ($vagas->localidades[$j] == ""){
	      							$localidade = "Região de ".$row['cidade']." / ".$this->ReplaceSiglas($row['estado']);
	      						} else if ($vagas->localidades[$j] == "Brasil"){
	      							$localidade = $vagas->localidades[$j]." / BR";
	      						} else {
	      							$tmp_cidade = explode(",", $vagas->localidades[$j]);
	      							$localidade = $tmp_cidade[0]." / ".$this->ReplaceSiglas($row['estado']);
	      						}

	      						$this->req_insertJob($vagas->codigos[$j], $vagas->titulos[$j], $vagas->empresas[$j], $vagas->resumos[$j], $localidade, $vagas->salarios[$j], $vagas->conteudos[$j], $vagas->links[$j], $row['cidade'], $this->ReplaceSiglas($row['estado']), $row['area'], $row['funcao'], $global);
								array_push($codigos_vagas, $vagas->codigos[$j]);

							} else {
								echo("Vaga [".++$global."](".$vagas->titulos[$j].") não anexada por já existir.\r\n");
							}
						}
      				}
      				unset($vagas);
				}

			} catch(PDOException $e) {
			    printf($e->getMessage()."\n");
			}
		}

		function insertJobs_estados($date_start, $date_end){
			try {
			   $pdo = new PDO("mysql:dbname=".$this->db.";host=".$this->host, $this->user, $this->pass);
			    $stmt = $pdo->query("SELECT area, funcao, estado, results FROM careernjob_jobsindeed_estados WHERE query_date BETWEEN '".$date_start."' and '".$date_end."'");
			    $global = 0;
			    $codigos_vagas = array();
				while ($row = $stmt->fetch()) {
				    $vagas = json_decode($row['results']);
      				for ($j = 0; $j < sizeof($vagas->codigos); $j++){
      					if ($vagas->conteudos[$j] == "" ||  $vagas->titulos[$j] == "" || $vagas->links[$j] == "")
						{
							echo("Vaga [".++$global."](".$vagas->titulos[$j].") não anexada por não estar nos moldes necessários.\r\n");
						} else {

							if (!in_array($vagas->codigos[$j], $codigos_vagas)){
								
								echo("Anexando vaga [".++$global."](".$vagas->titulos[$j].")...\r\n");
	      						
	      						if ($vagas->localidades[$j] == ""){
	      							$localidade = "Região de(o) ".$row['estado']." / ".$this->ReplaceSiglas($row['estado']);
	      						} else if ($vagas->localidades[$j] == "Brasil"){
	      							$localidade = $vagas->localidades[$j]." / BR";
	      						} else {
	      							$tmp_cidade = explode(",", $vagas->localidades[$j]);
	      							$localidade = $tmp_cidade[0]." / ".$this->ReplaceSiglas($row['estado']);
	      						}

	      						$this->req_insertJob($vagas->codigos[$j], $vagas->titulos[$j], $vagas->empresas[$j], $vagas->resumos[$j], $localidade, $vagas->salarios[$j], $vagas->conteudos[$j], $vagas->links[$j], 'Região de(o) '.$row['estado'], $this->ReplaceSiglas($row['estado']), $row['area'], $row['funcao'], $global);
								array_push($codigos_vagas, $vagas->codigos[$j]);

							} else {
								echo("Vaga [".++$global."](".$vagas->titulos[$j].") não anexada por já existir.\r\n");
							}
						}
      				}
      				unset($vagas);
				}

			} catch(PDOException $e) {
			    printf($e->getMessage()."\n");
			}
		}

		private function async_req_insertJob($codigo, $titulo, $empresa, $resumo, $localidade, $salario, $conteudo, $link, $cidade, $estado, $area, $funcao){

			$this->validateToken($this->token_now);

			if ($this->token_valid == 1){
				$process = curl_init($this->baseurlwp.'/wp-json/api/createjobindeedasync');
				$data = array(
					'codigo' => $codigo,
					'titulo' => $titulo,
					'empresa' => $empresa,
					'resumo' => $resumo,
					'localidade' => $localidade,
					'salario' => $salario,
					'conteudo' => $conteudo,
					'link' => $link,
					'cidade' => $cidade,
					'estado' => $estado,
					'area' => $area,
					'funcao' => $funcao
				);
				$data_string = json_encode($data);
				curl_setopt($process, CURLOPT_TIMEOUT, 30);
				curl_setopt($process, CURLOPT_POST, 1);
				curl_setopt($process, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($process, CURLOPT_POSTFIELDS, $data_string);
				curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);	
				curl_setopt($process, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json', 'Authorization: Bearer '.$this->token_now, 'Content-Length: '.strlen($data_string)));

				return($process);
			} else {
				printf("Erro ao validar token\n");
				return("error");
			}

		}

		function async_insertJobs($date_start, $date_end){
			try {
			    $pdo = new PDO("mysql:dbname=".$this->db.";host=".$this->host, $this->user, $this->pass);
			    $stmt = $pdo->query("SELECT area, funcao, cidade, estado, results FROM careernjob_jobsindeed WHERE query_date BETWEEN '".$date_start."' and '".$date_end."'");
			    $global = 1;
			    $session = curl_multi_init();
			    $requests = array();
			    $codigos_vagas = array();
			    $localidade = null;
				while ($row = $stmt->fetch()) {
					$vagas = json_decode($row['results']);
					for ($j = 0; $j < sizeof($vagas->codigos); $j++){
						if ($vagas->conteudos[$j] == "" ||  $vagas->titulos[$j] == "" || $vagas->links[$j] == "")
						{
							echo("Vaga [".$global++."](".$vagas->titulos[$j].") não anexada por não estar nos moldes necessários.\r\n");
						} else {

							if (!in_array($vagas->codigos[$j], $codigos_vagas)){
								
								echo("Anexando vaga [".$global++."](".$vagas->titulos[$j].")...\r\n");
	      						
	      						if ($vagas->localidades[$j] == ""){
	      							$localidade = "Região de ".$row['cidade']." / ".$this->ReplaceSiglas($row['estado']);
	      						} else if ($vagas->localidades[$j] == "Brasil"){
	      							$localidade = $vagas->localidades[$j]." / BR";
	      						} else {
	      							$tmp_cidade = explode(",", $vagas->localidades[$j]);
	      							$localidade = $tmp_cidade[0]." / ".$this->ReplaceSiglas($row['estado']);
	      						}

	      						$req = $this->async_req_insertJob($vagas->codigos[$j], $vagas->titulos[$j], $vagas->empresas[$j], $vagas->resumos[$j], $localidade, $vagas->salarios[$j], $vagas->conteudos[$j], $vagas->links[$j], $row['cidade'], $this->ReplaceSiglas($row['estado']), $row['area'], $row['funcao']);
	      						
	      						$requests[] = $req;
								curl_multi_add_handle($session, $req);
								array_push($codigos_vagas, $vagas->codigos[$j]);

							} else {
								echo("Vaga [".$global++."](".$vagas->titulos[$j].") não anexada por já existir.\r\n");
							}
						}
					}
				}

				printf("Processando...\n");
				do {
				    curl_multi_exec($session, $running);
				    curl_multi_select($session);
				} while ($running > 0);
				printf("Processamento finalizado...\n");

				foreach ($requests as $request){
					curl_multi_remove_handle($session, $request);
				}
				curl_multi_close($session);
				printf("Recursos liberados...\n");

			} catch(PDOException $e) {
			    printf($e->getMessage()."\n");
			}
		}

		function async_insertJobs_estados($date_start, $date_end){
			try {
			    $pdo = new PDO("mysql:dbname=".$this->db.";host=".$this->host, $this->user, $this->pass);
			    $stmt = $pdo->query("SELECT area, funcao, estado, results FROM careernjob_jobsindeed_estados WHERE query_date BETWEEN '".$date_start."' and '".$date_end."'");
			    $global = 1;
			    $session = curl_multi_init();
			    $requests = array();
			    $codigos_vagas = array();
			    $localidade = null;
				while ($row = $stmt->fetch()) {
					$vagas = json_decode($row['results']);
					for ($j = 0; $j < sizeof($vagas->codigos); $j++){
						if ($vagas->conteudos[$j] == "" ||  $vagas->titulos[$j] == "" || $vagas->links[$j] == "")
						{
							echo("Vaga [".$global++."](".$vagas->titulos[$j].") não anexada por não estar nos moldes necessários.\r\n");
						} else {

							if (!in_array($vagas->codigos[$j], $codigos_vagas)){
								
								echo("Anexando vaga [".$global++."](".$vagas->titulos[$j].")...\r\n");
	      						
	      						if ($vagas->localidades[$j] == ""){
	      							$localidade = "Região de(o) ".$row['estado']." / ".$this->ReplaceSiglas($row['estado']);
	      						} else if ($vagas->localidades[$j] == "Brasil"){
	      							$localidade = $vagas->localidades[$j]." / BR";
	      						} else {
	      							$tmp_cidade = explode(",", $vagas->localidades[$j]);
	      							$localidade = $tmp_cidade[0]." / ".$this->ReplaceSiglas($row['estado']);
	      						}

	      						$req = $this->async_req_insertJob($vagas->codigos[$j], $vagas->titulos[$j], $vagas->empresas[$j], $vagas->resumos[$j], $localidade, $vagas->salarios[$j], $vagas->conteudos[$j], $vagas->links[$j], 'Região de(o) '.$row['estado'], $this->ReplaceSiglas($row['estado']), $row['area'], $row['funcao']);
	      						
	      						$requests[] = $req;
								curl_multi_add_handle($session, $req);
								array_push($codigos_vagas, $vagas->codigos[$j]);

							} else {
								echo("Vaga [".$global++."](".$vagas->titulos[$j].") não anexada por já existir.\r\n");
							}
						}
					}
				}

				printf("Processando...\n");
				do {
				    curl_multi_exec($session, $running);
				    curl_multi_select($session);
				} while ($running > 0);
				printf("Processamento finalizado...\n");

				foreach ($requests as $request){
					curl_multi_remove_handle($session, $request);
				}
				curl_multi_close($session);
				printf("Recursos liberados...\n");

			} catch(PDOException $e) {
			    printf($e->getMessage()."\n");
			}
		}

	}

	/*
			
		Implementação Big Data Estados - $implements->getStartCrawler('https://www.trabalhabrasil.com.br/busca-de-vagas');
		Implementação Big Data Área - $implements->getLinksArea('https://www.trabalhabrasil.com.br/busca-de-vagas-area');
		Implementação Estado - $implements->getLinksCidades('https://www.trabalhabrasil.com.br/vagas-por-estado/acre');
		Implementação Cidade - $implements->getJobs('https://www.trabalhabrasil.com.br/vagas-empregos-em-acrelandia-ac');
		Implementação Área - $implements->getLinksFuncao('https://www.trabalhabrasil.com.br/busca-de-vagas-funcao/administrativo');
		Implementação Função - $implements->getJobs('https://www.trabalhabrasil.com.br/vagas-empregos/agente-de-controle-de-qualidade');
		
	*/

?>