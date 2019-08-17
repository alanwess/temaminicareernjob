<?php
	
	class CrawlerTrabalhaBrasil { 

		// Base URL Crawler
		private $baseurl = 'https://www.trabalhabrasil.com.br';

		//Estrutura de dados das vaga
		private $codigos = array();
		private $titulos = array();
		private $descricoes = array();
		private $localidades = array();
		private $tags = array();
		private $links = array();
		
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

		public function getLinksEstado($start_url){

			$options = array('http'=>array('method'=>"GET", 'header'=>"User-Agent: Doodle Search/0.1\n"));
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
						printf("Estado (".++$count.") para '".$this->baseurl.$el->getAttribute("href")."'\r\n");
						$this->getLinksCidades($this->baseurl.$el->getAttribute("href"));
					}
				}
			}

		}

		public function getLinksCidades($estado_url){

			$options = array('http'=>array('method'=>"GET", 'header'=>"User-Agent: Doodle Search/0.1\n"));
			$context = stream_context_create($options);

			$doc = new DomDocument();
			@$doc->loadHTML(file_get_contents($estado_url, false, $context));
			$finder = new DomXPath($doc);
			$classname="card__jobs__links";
			$elementos  = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
			$count = 0;
			foreach ($elementos as $node) 
			{
				foreach ($node->childNodes as $el){
					if ($el->nodeName == "a"){
						printf("Cidade (".++$count.") para '".$this->baseurl.$el->getAttribute("href")."'\r\n");
						$this->getJobs($this->baseurl.$el->getAttribute("href"));
					}
				}
			}

		}

		public function getLinksEstado_array($start_url){

			$options = array('http'=>array('method'=>"GET", 'header'=>"User-Agent: Doodle Search/0.1\n"));
			$context = stream_context_create($options);

			$doc = new DomDocument();
			@$doc->loadHTML(file_get_contents($start_url, false, $context));
			$finder = new DomXPath($doc);
			$classname="card__jobs__links";
			$elementos  = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
			$count = 0;
			$linksEstados = array();
			foreach ($elementos as $node) 
			{
				foreach ($node->childNodes as $el){
					if ($el->nodeName == "a"){
						$linksEstados[] = $this->baseurl.$el->getAttribute("href");
					}
				}
			}
			return $linksEstados;

		}

		public function getLinksCidades_array($estado_url){

			$options = array('http'=>array('method'=>"GET", 'header'=>"User-Agent: Doodle Search/0.1\n"));
			$context = stream_context_create($options);

			$doc = new DomDocument();
			@$doc->loadHTML(file_get_contents($estado_url, false, $context));
			$finder = new DomXPath($doc);
			$classname="card__jobs__links";
			$elementos  = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
			$count = 0;
			$linksCidades = array();
			foreach ($elementos as $node) 
			{
				foreach ($node->childNodes as $el){
					if ($el->nodeName == "a"){
						if (strpos($el->getAttribute("href"), "busca") == FALSE){
							$linksCidades[] = $this->baseurl.$el->getAttribute("href");
						}
					}
				}
			}
			return $linksCidades;

		}

		public function getLinksArea($start_url){

			$options = array('http'=>array('method'=>"GET", 'header'=>"User-Agent: Doodle Search/0.1\n"));
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
						printf("Area (".++$count.") para '".$this->baseurl.$el->getAttribute("href")."'\r\n");
						$this->getLinksFuncao($this->baseurl.$el->getAttribute("href"));
					}
				}
			}

		}

		public function getLinksFuncao($area_url){

			$options = array('http'=>array('method'=>"GET", 'header'=>"User-Agent: Doodle Search/0.1\n"));
			$context = stream_context_create($options);

			$doc = new DomDocument();
			@$doc->loadHTML(file_get_contents($area_url, false, $context));
			$finder = new DomXPath($doc);
			$classname="card__jobs__links";
			$elementos  = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
			$count = 0;
			foreach ($elementos as $node) 
			{
				foreach ($node->childNodes as $el){
					if ($el->nodeName == "a"){
						printf("Funcao (".++$count.") para '".$this->baseurl.$el->getAttribute("href")."'\r\n");
						$this->getJobs($this->baseurl.$el->getAttribute("href"));
					}
				}
			}

		}

		// Pega listagem de vagas por cidade
		public function getJobs($cidade_url){

			$options = array('http'=>array('method'=>"GET", 'header'=>"User-Agent: Doodle Search/0.1\n"));
			$context = stream_context_create($options);
			$cidades = array();

			$doc = new DomDocument();
			@$doc->loadHTML(file_get_contents($cidade_url, false, $context));
			$finder = new DomXPath($doc);
			$classname="job-wrapper";
			$elementos  = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
			$count = 0;
			foreach ($elementos as $node) 
			{
				foreach ($node->childNodes as $el){
					if ($el->nodeName == "a" && $el->getAttribute("class") == "job-vacancy "){
						printf("Vaga (".++$count.") para '".$this->baseurl.$el->getAttribute("href")."'\r\n");
						$this->getJob($this->baseurl.$el->getAttribute("href"));
					}
				}
			}

			$finder = new DomXPath($doc);
			$classname="scoreboard";
			$elementos  = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
			foreach ($elementos as $node) 
			{
				foreach ($node->childNodes as $el){
					if ($el->nodeName == "h1"){
						return str_replace('Vagas de Emprego em ', '', preg_replace('/[0-9]+/', '', $el->nodeValue));
					}
				}
			}
		}

		public function getJob($url, $nivel = 0){
			$options = array('http'=>array('method'=>"GET", 'header'=>"User-Agent: Doodle Search/0.1\n"));
			$context = stream_context_create($options);

			$doc = new DomDocument();
			@$doc->loadHTML(file_get_contents($url, false, $context));
			$finder = new DomXPath($doc);

			$tmp_titulo = trim(str_replace("accessible", "", $this->getTitle($finder)));
			$array_titulo = explode(" EM ", $tmp_titulo);

			$this->titulos[] = ucfirst(mb_strtolower(trim($array_titulo[0])));
			$array_loc_cod = explode(" - ", $array_titulo[1]);

			$this->localidades[] = ucwords(mb_strtolower(trim($array_loc_cod[0])));
			$this->codigos[] = str_replace("CÓD. ","",trim($array_loc_cod[1]));
			$this->descricoes[] = $this->getDescricao($finder);
			$this->tags[] = $this->getTags($finder);
			$this->links[] = $url;

			printf("Vaga (".$url.") processada\r\n");

			$links_relacionados = $this->getRelated($finder);

			if($nivel < 0){
				foreach ($links_relacionados as $link_relacionado) {
					$this->getJob($link_relacionado, $nivel+1);
				}
			}
		}

		private function getTitle($finder){
	
			$classname = "job-title";
			$elementos = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
			$content = "";
			foreach ($elementos as $node) { if ($node->nodeValue != "") $content = $node->nodeValue; }
			return $content;

		}

		private function getDescricao($finder){

			$classname = "job-plain-text";
			$elementos  = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
			$content = "";
			foreach ($elementos as $node) 
			{
				if(!is_numeric(trim($node->nodeValue)) && $node->nodeValue != "Disponível apenas para cadastrados.") $content .= $node->nodeValue.'<br>';
			}
			$content = str_replace("Disponível apenas para cadastrados.", "", $content);
			return $content;

		}

		private function getTags($finder){

			$classname = "map";
			$elementos  = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
			$node = $elementos[0];

			$tags = array();
			if ($node->nodeName == "div"){
				foreach ($node->childNodes as $el) 
				{
					if ($el->nodeName == "div"){
						foreach ($el->childNodes as $el2) 
						{	
							if ($el2->nodeName == "a"){
								$tags[] = trim($el2->nodeValue);
							}
						}
					}
				}
			}

			return $tags;

		}

		private function getRelated($finder){

			$classname="more-job";
			$elementos  = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
			$links = array();
			foreach ($elementos as $node) 
			{
				if ($node->nodeName == "a"){
					$links[] = $this->baseurl.$node->getAttribute("href");
				}
			}
			return $links;

		}

		public function exportData(){
			return(array($this->codigos, $this->titulos, $this->descricoes, $this->localidades, $this->tags, $this->links));
		}

		public function exportDataJSON(){
			return(
				json_encode(
					array(
						"codigos" 	  => $this->codigos, 
						"titulos"	  => $this->titulos, 
						"descricoes"  => $this->descricoes, 
						"localidades" => $this->localidades, 
						"tags" 		  => $this->tags, 
						"links"		  => $this->links
					)
				)
			);
		}

		public function exportDataJSON_other(){
			return(
				json_encode(array($this->codigos, $this->titulos, $this->descricoes, $this->localidades, $this->tags, $this->links))
			);
		}

		public function setJobs($localidade){
	
			try {
			    $pdo = new PDO("mysql:dbname=".$this->db.";host=".$this->host, $this->user, $this->pass);
			    $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );//Error Handling
			    $sql = "CREATE TABLE IF NOT EXISTS careernjob_jobstrabalhabrasil (
			     	Id INT( 11 ) AUTO_INCREMENT PRIMARY KEY,
			     	localidade TEXT, 
			    	results LONGTEXT, 
			    	query_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci" ;
			    $pdo->exec($sql);
			    $sql = "INSERT INTO careernjob_jobstrabalhabrasil (localidade, results) VALUES (:localidade, :results)" ;
			    $stmt = $pdo->prepare($sql);    
			    $consulta = $this->exportDataJSON(); 
			    $stmt->bindParam(':localidade', $localidade, PDO::PARAM_STR);                          
				$stmt->bindParam(':results', $consulta, PDO::PARAM_STR);                                  
				$stmt->execute(); 
			} catch(PDOException $e) {
			    printf($e->getMessage()."\n");
			}

		}

		public function setJobs_other($localidade){
	
			try {
			    $pdo = new PDO("mysql:dbname=".$this->db.";host=".$this->host, $this->user, $this->pass);
			    $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );//Error Handling
			    $sql = "CREATE TABLE IF NOT EXISTS careernjob_jobstrabalhabrasil (
			     	Id INT( 11 ) AUTO_INCREMENT PRIMARY KEY,
			     	localidade TEXT, 
			    	results LONGTEXT, 
			    	query_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci" ;
			    $pdo->exec($sql);
			    $sql = "INSERT INTO careernjob_jobstrabalhabrasil (localidade, results) VALUES (:localidade, :results)" ;
			    $stmt = $pdo->prepare($sql);    
			    $consulta = $this->exportDataJSON_other(); 
			    $stmt->bindParam(':localidade', $localidade, PDO::PARAM_STR);                          
				$stmt->bindParam(':results', $consulta, PDO::PARAM_STR);                                  
				$stmt->execute(); 
			} catch(PDOException $e) {
			    printf($e->getMessage()."\n");
			}

		}

		public function getJobsDB($date_start, $date_end){
			try {
				$pdo = new PDO("mysql:dbname=".$this->db.";host=".$this->host, $this->user, $this->pass);
			    $stmt = $pdo->query("SELECT localidade, results FROM careernjob_jobstrabalhabrasil WHERE query_date BETWEEN '".$date_start."' and '".$date_end."'");
			    $global = 1;
				while ($row = $stmt->fetch()) {
				    printf("Inicio da nova linha do banco (Resgatando cidade: ".$row['localidade'].")\r\n");
				    $vagas = json_decode($row['results']);
				    for ($j = 0; $j < sizeof($vagas->codigos); $j++){
				    	printf("Contador: ".$global++."\n");
				    	printf("Código: ".$vagas->codigos[$j]."\r\n");
						printf("Titulo: ".$vagas->titulos[$j]."\r\n");
						printf("Descrição: ".$vagas->descricoes[$j]."\r\n");
						printf("Localidade: ".$vagas->localidades[$j]."\r\n");
						printf("Tags: \r\n");	
						foreach ($vagas->tags[$j] as $key){
							printf("Keyword: ".$key."\r\n");
						}
						printf("Link: ".$vagas->links[$j]."\r\n");
						printf("\n\n");
					}
				}
			} catch(PDOException $e) {
			    printf($e->getMessage()."\n");
			}
		}

		public function getJobsDB_other($date_start, $date_end){
			try {
				$pdo = new PDO("mysql:dbname=".$this->db.";host=".$this->host, $this->user, $this->pass);
			    $stmt = $pdo->query("SELECT localidade, results FROM careernjob_jobstrabalhabrasil WHERE query_date BETWEEN '".$date_start."' and '".$date_end."'");
			    $global = 1;
				while ($row = $stmt->fetch()) {
				    printf("Inicio da nova linha do banco (Resgatando cidade: ".$row['localidade'].")\r\n");
				    $vagas = json_decode($row['results']);
				    for ($j = 0; $j < sizeof($vagas->codigos); $j++){
				    	printf("Contador: ".$global++."\n");
				    	printf("Código: ".$vagas[0][$j]."\r\n");
						printf("Titulo: ".$vagas[1][$j]."\r\n");
						printf("Descrição: ".$vagas[2][$j]."\r\n");
						printf("Localidade: ".$vagas[3][$j]."\r\n");
						printf("Tags: \r\n");	
						foreach ($vagas[4][$j] as $key){
							printf("Keyword: ".$key."\r\n");
						}
						printf("Link: ".$vagas[5][$j]."\r\n");
						printf("\n\n");
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

		private function req_insertJob($codigo, $titulo, $descricao, $localidade, $tags, $link, $count){

			$this->validateToken($this->token_now);

			if ($this->token_valid == 1){
				$process = curl_init($this->baseurlwp.'/wp-json/api/createjobtb');
				$data = array(
					'codigo' => $codigo,
					'titulo' => $titulo,
					'descricao' => $descricao,
					'localidade' => $localidade,
					'tags' => $tags,
					'link' => $link
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
			    $stmt = $pdo->query("SELECT localidade, results FROM careernjob_jobstrabalhabrasil WHERE query_date BETWEEN '".$date_start."' and '".$date_end."'");
			    $global = 1;
			    $codigos_vagas = array();
				while ($row = $stmt->fetch()) {
				    $vagas = json_decode($row['results']);
      				for ($j = 0; $j < sizeof($vagas->codigos); $j++){
      					if (!in_array($vagas->codigos[$j], $codigos_vagas)){
      						$this->req_insertJob($vagas->codigos[$j], $vagas->titulos[$j], $vagas->descricoes[$j], $vagas->localidades[$j], $vagas->tags[$j], $vagas->links[$j], $global++);
      						array_push($codigos_vagas, $vagas->codigos[$j]);
						} else {
							printf("Vaga [".$global++."](".$vagas->titulos[$j].") não encaminhada por já existir.\r\n");
						}
      				}
      				unset($noticias);
				}

			} catch(PDOException $e) {
			    printf($e->getMessage()."\n");
			}
		}

		private function async_req_insertJob($codigo, $titulo, $descricao, $localidade, $tags, $link){

			$this->validateToken($this->token_now);

			if ($this->token_valid == 1){
				$process = curl_init($this->baseurlwp.'/wp-json/api/createjobtbasync');
				$data = array(
					'codigo' => $codigo,
					'titulo' => $titulo,
					'descricao' => $descricao,
					'localidade' => $localidade,
					'tags' => $tags,
					'link' => $link
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
			    $stmt = $pdo->query("SELECT localidade, results FROM careernjob_jobstrabalhabrasil WHERE query_date BETWEEN '".$date_start."' and '".$date_end."'");
			    $global = 1;
			    $session = curl_multi_init();
			    $requests = array();
			    $codigos_vagas = array();
				while ($row = $stmt->fetch()) {
					$vagas = json_decode($row['results']);
				    for ($j = 0; $j < sizeof($vagas->codigos); $j++){
				    	if (!in_array($vagas->codigos[$j], $codigos_vagas)){
				    		printf("Anexando vaga [".$global++."](".$vagas->titulos[$j].")...\r\n");
      						$req = $this->async_req_insertJob($vagas->codigos[$j], $vagas->titulos[$j], $vagas->descricoes[$j], $vagas->localidades[$j], $vagas->tags[$j], $vagas->links[$j]);
      						$requests[] = $req;
							curl_multi_add_handle($session, $req);
							array_push($codigos_vagas, $vagas->codigos[$j]);
						} else {
							printf("Vaga [".$global++."](".$vagas->titulos[$j].") não anexada por já existir.\r\n");
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

		private function async_req_insertJob_json($resultsjson){

			$this->validateToken($this->token_now);

			if ($this->token_valid == 1){
				$process = curl_init($this->baseurlwp.'/wp-json/api/createjobtbasyncjson');
				$data = array(
					'results' => $resultsjson 
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

		function async_insertJobs_json($date_start, $date_end){
			try {
			    $pdo = new PDO("mysql:dbname=".$this->db.";host=".$this->host, $this->user, $this->pass);
			    $stmt = $pdo->query("SELECT localidade, results FROM careernjob_jobstrabalhabrasil WHERE query_date BETWEEN '".$date_start."' and '".$date_end."'");
			    $global = 1;
			    $session = curl_multi_init();
			    $requests = array();
				while ($row = $stmt->fetch()) {
					$req = $this->async_req_insertJob_json($row['results']);
					$requests[] = $req;
				    curl_multi_add_handle($session, $req);
				    printf("Resultados da consulta (".$global++.") para localidade [".$row['localidade']."] anexada\n");
				}
				
				printf("Processando...\n");
				do {
				    curl_multi_exec($session, $running);
				    curl_multi_select($session);
				} while ($running > 0);
				printf("Processamento finalizado...\n");
				
				foreach ($requests as $request){
					//echo curl_multi_getcontent($request);
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