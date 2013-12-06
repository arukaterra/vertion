<?php


class db{
	
	var $lastid = false;
	//sementara mysql dulu
	public function connect($port=0){
		GLOBAL $config;
		$link = mysql_connect($config['DATABASE'][$port]['HOST'],$config['DATABASE'][$port]['USERNAME'],$config['DATABASE'][$port]['PASSWORD']);
		mysql_select_db($config['DATABASE'][$port]['DB']);
		if (!$link) {
			// pr($config['DATABASE'][$port]);
			die('Could not connect: ' . mysql_error());
		}
	}
	
	public function close(){
		mysql_close();
	}	
	
	public function query($query=NULL){
		if($query==NULL) return false;
		$this->connect();
			$data = mysql_query($query);
			// pr($data);
			$lastid = mysql_insert_id();
			if($lastid) $this->setLastinsertID($lastid);
			else $this->setLastinsertID();
		$this->close();
		return $data;
	}
	
	public function fetch($query=NULL,$all=FALSE){
		if($query==NULL) return false;
		$data = null;
		$queryThis = $this->query($query);
		if($queryThis){
		$this->connect();
			if($all==TRUE) {
				while($row = mysql_fetch_object($queryThis)){
					$data[] = $row;
				}
			}else $data = mysql_fetch_object($queryThis);
			mysql_free_result($queryThis);
		$this->close();
		}		
		return $data;
	}
	
	function setLastinsertID($theid = false){
		$this->lastid = $theid;
	}
	function getLastinsertID(){
		return $this->lastid;
	}
}


?>