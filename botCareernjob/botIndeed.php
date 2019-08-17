<?php		 
	
	include_once("CrawlerIndeed/CrawlerIndeed.php");

	class botIndeed { 

		private $db;
		private $host;
		private $user;
		private $pass;
		private $username;
		private $password;
		private $baseurlwp;

		public function __construct($db, $host, $user, $pass, $username, $password, $baseurlwp){

			$this->db = $db;
			$this->host = $host;
			$this->user = $user;
			$this->pass = $pass;
			$this->username = $username;
			$this->password = $password;
			$this->baseurlwp = $baseurlwp;

		}

		public function setVagas($estados_arr, $cidades_arr, $areas_arr, $funcoes_arr){
			
			$implements = new CrawlerIndeed($this->db, $this->host, $this->user, $this->pass, $this->username, $this->password, $this->baseurlwp);
			$implements->estados = $estados_arr;
			$implements->cidades = $cidades_arr;
			$implements->areas = $areas_arr;
			$implements->funcoes = $funcoes_arr;
			$implements->exploreWorld();

		}

		public function setVagas_estados($estados_arr, $areas_arr, $funcoes_arr){
			
			$implements = new CrawlerIndeed($this->db, $this->host, $this->user, $this->pass, $this->username, $this->password, $this->baseurlwp);
			$implements->estados = $estados_arr;
			$implements->areas = $areas_arr;
			$implements->funcoes = $funcoes_arr;
			$implements->exploreWorld_estados();

		}

		public function insertWPVagas(){

			$implements = new CrawlerIndeed($this->db, $this->host, $this->user, $this->pass, $this->username, $this->password, $this->baseurlwp);
			$implements->insertJobs(date('Y-m-d 00:00:00', time()),date('Y-m-d 23:59:59', time()));

		}

		public function insertWPVagas_estados(){

			$implements = new CrawlerIndeed($this->db, $this->host, $this->user, $this->pass, $this->username, $this->password, $this->baseurlwp);
			$implements->insertJobs_estados(date('Y-m-d 00:00:00', time()),date('Y-m-d 23:59:59', time()));

		}

		public function insertWPVagas_async(){

			$implements = new CrawlerIndeed($this->db, $this->host, $this->user, $this->pass, $this->username, $this->password, $this->baseurlwp);
			$implements->async_insertJobs(date('Y-m-d 00:00:00', time()),date('Y-m-d 23:59:59', time()));

		}

		public function insertWPVagas_async_estados(){

			$implements = new CrawlerIndeed($this->db, $this->host, $this->user, $this->pass, $this->username, $this->password, $this->baseurlwp);
			$implements->async_insertJobs_estados(date('Y-m-d 00:00:00', time()),date('Y-m-d 23:59:59', time()));

		}

	}

?>