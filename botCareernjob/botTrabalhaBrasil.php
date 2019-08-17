<?php		  
	
	include_once("CrawlerTrabalhaBrasil/CrawlerTrabalhaBrasil.php");

	class botTrabalhaBrasil { 

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

		public function setVagas($estado_url){
			
			$implements = new CrawlerTrabalhaBrasil($this->db, $this->host, $this->user, $this->pass, $this->username, $this->password, $this->baseurlwp);
			$linkscidades = $implements->getLinksCidades_array($estado_url);
	
			foreach ($linkscidades as $linkcidade) {
				$implements->setJobs($implements->getJobs($linkcidade));
			}

		}

		public function getEstadosArr($start_url){

			$implements = new CrawlerTrabalhaBrasil($this->db, $this->host, $this->user, $this->pass, $this->username, $this->password, $this->baseurlwp);
			
			return $implements->getLinksEstado_array($start_url);

		}

		public function insertWPVagas(){

			$implements = new CrawlerTrabalhaBrasil($this->db, $this->host, $this->user, $this->pass, $this->username, $this->password, $this->baseurlwp);
			$implements->insertJobs(date('Y-m-d 00:00:00', time()),date('Y-m-d 23:59:59', time()));

		}

		public function insertWPVagas_async(){

			$implements = new CrawlerTrabalhaBrasil($this->db, $this->host, $this->user, $this->pass, $this->username, $this->password, $this->baseurlwp);
			$implements->async_insertJobs(date('Y-m-d 00:00:00', time()),date('Y-m-d 23:59:59', time()));

		}

	}

?>