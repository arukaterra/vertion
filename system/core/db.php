<?php


class db{
	
	var $lastid = false;
	var $link=false;
	var $port=false;
	//sementara mysql dulu
	public function connect($port=0){
		GLOBAL $config;
		$this->port = $port;
		if($this->link==false){
			$this->link = mysql_connect($config['DATABASE'][$port]['HOST'],$config['DATABASE'][$port]['USERNAME'],$config['DATABASE'][$port]['PASSWORD']);
			mysql_select_db($config['DATABASE'][$port]['DB']);
		}
		if (!$this->link) {
			// pr($config['DATABASE'][$port]);
			die('Could not connect: ' . mysql_error());
		}
	}
	
	public function close(){
		if($this->link!=false){
			mysql_close($this->link);
			$this->link = false;
		}
	}	
	
	public function query($query=NULL){
		if($query==NULL) return false;
		if($this->link==false) $this->connect($this->port);
			$data = mysql_query($query);
			// pr($data);
			$lastid = mysql_insert_id();
			if($lastid) $this->setLastinsertID($lastid);
			else $this->setLastinsertID();
		if($this->link==false) $this->close();
		return $data;
	}
	
	public function fetch($query=NULL,$all=FALSE){
		if($query==NULL) return false;
		$data = null;
		$queryThis = $this->query($query);
		if($queryThis){
		if($this->link==false) $this->connect($this->port);
			if($all==TRUE) {
				while($row = mysql_fetch_array($queryThis,MYSQL_ASSOC)){
					$data[] = $row;
				}
			}else $data = mysql_fetch_array($queryThis,MYSQL_ASSOC);
			mysql_free_result($queryThis);
		if($this->link==false) $this->close();
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