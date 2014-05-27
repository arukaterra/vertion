<?php 

class usersAuth {
	
	
	function __construct($apps=false){  
		if($apps) $this->apps = $apps;
	}
	
	function checklogin(){
		 
		if($this->apps->session->getSession('is_login')) return true;
		else return false;
	}

	function getLoginUser(){
	
		global $config,$DATABASE;
			
		$username = strip_tags(_p('username'));
		$password = strip_tags(_p('password'));
				 
		$sql = "
		SELECT salt,password, createddate,p.*
		FROM {$DATABASE['MAJOR']}._user s
		LEFT JOIN {$DATABASE['MAJOR']}._user_profile p ON s.id = p.userid
		WHERE 
		s.username ='{$username}' AND p.username ='{$username}' LIMIT 1";
 
		$qData = $this->apps->fetch($sql);
		
		if($qData) {
			$hashPass =sha1(sha1($password.$qData['salt']."{".$username)."}".$qData['createddate']);
		
			if($qData['password']!=$hashPass) return false;	
			
			return $qData;
		}else return false;
		
	}
	
	
	function AuthLogin(){
			
		//function untuk login
	 
		$qData = $this->getLoginUser();
			 // pr($qData);
		 // exit;
		if($qData)	{
			 
			$this->apps->session->setSession('is_login',true);
			$this->apps->session->setSession('users',$qData);	
		}else{
			$this->apps->session->setSession('is_login',false);
		}
		
		if($this->apps->session->getSession('is_login')){	 
			$this->apps->session->setSession('verified',1);
		
			gotoPage(BASE_PATH."homepage"); 
			exit;
		}
		gotoPage(BASE_PATH."login"); 
		exit;
	}
	
	
	function doRegister(){
		
		global $config,$DATABASE;
		$data['status'] = false;
		$data['message'] = "faild to register";
		
		$gender = _p('gender');
		$username = _p('username');
		$password = _p('password');
		$email = _p('email');
		$ipaddress = $_SERVER['REMOTE_ADDR'];
		$createdDate = date('Y-m-d H:i:s');
		$salt = sha1(sha1($password.sha1($config['SALT']).$username).$createdDate);
		$hashPass = sha1(sha1($password.$salt."{".$username)."}".$createdDate);
		
	
		$sql = "
			INSERT INTO {$DATABASE['MAJOR']}.`_user` 
			(  `username`, `password`, `salt`, `lastlogin`, `createddate`, `try_to_login`, `nstatus`) 
			VALUES 
			(  '{$username}', '{$hashPass}', '{$salt}', '{$createdDate}', '{$createdDate}', '0', '1') 
		";
		
		$qData = $this->apps->query($sql);
		$userid = $this->apps->getLastinsertID();
		if(!$userid) return $data;
		$sql ="
		INSERT INTO {$DATABASE['MAJOR']}.`_user_profile` ( `userid`, `name`, `lastname`, `nickname`, `email`, `birthday`, `gender`, `liveat`, `username`, `modifieddate`, `nstatus`) 
		VALUES (  '{$userid}', '{$username}', '', '{$username}', '{$email}', '{$createdDate}', '{$gender}', '0', '{$username}', '{$createdDate}', '1')";
		
		$qData = $this->apps->query($sql);
		$profileid = $this->apps->getLastinsertID();
		 
		if($profileid) $this->AuthLogin();
		return $data;
	}
	


}




?>