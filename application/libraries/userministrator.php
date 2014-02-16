<?php 

class Userministrator {
	
	
	function __construct($apps=false){  
		if($apps) $this->apps = $apps;
	}
	
	function checklogin(){
		 
		if($this->apps->session->getSession('is_login')) return true;
		else return false;
	}

	function getLoginUser(){
	
		global $config,$DATABASE;
			
		$username = _p('username');
		$password = _p('password');
				 
		$sql = "SELECT * FROM {$DATABASE['MAJOR']}._user WHERE username ='{$username}' LIMIT 1";
 
		$qData = $this->apps->fetch($sql);
		
		if($qData) {
			$hashPass = sha1(sha1($password.sha1($config['SALT']).$username).$qData->createddate );
		
			if($qData->password!=$hashPass) return false;	
			
			return $qData;
		}else return false;
		
	}
	
	
	function doRegister(){
		
		global $config,$DATABASE;
		$username = _p('username');
		$password = _p('password');
		$email = _p('email');
		$ipaddress = $_SERVER['REMOTE_ADDR'];
		$createdDate = date('Y-m-d H:i:s');
		$hashPass = sha1(sha1($password.sha1($config['SALT']).$username).$createdDate);
	
		$sql = "
			INSERT INTO {$DATABASE['MAJOR']}._user 
			(email ,	username ,	password 	,IPAddress, 	lastLogin, 	createdDate ,	modifiedDate ,	starusID )
			VALUES
			('{$email}','{$username}','{$hashPass}','{$ipaddress}','0000-00-00','{$createdDate}','{$createdDate}',1)
		";
		
		$qData = $this->apps->query($sql);
		gotoPage(BASE_PATH.'login');
	}
	


}




?>