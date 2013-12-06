<?php 

class Userministrator  extends db {



	function getLoginUser(){
		global $config,$DATABASE;
			
		$username = methodPost('username');
		$password = methodPost('password');
				
		$sql = "SELECT * FROM {$DATABASE['MAJOR']}._user WHERE username ='{$username}' LIMIT 1";
		
		$qData = $this->fetch($sql);
		if($qData) {
			$hashPass = sha1(sha1($password.sha1($config['SALT']).$username).$qData->createdDate);
			if($qData->password!=$hashPass) return false;	
			
			return $qData;
		}else return false;
		
	}
	
	
	function doRegister(){
		
		global $config,$DATABASE;
		$username = methodPost('username');
		$password = methodPost('password');
		$email = methodPost('email');
		$ipaddress = $_SERVER['REMOTE_ADDR'];
		$createdDate = date('Y-m-d H:i:s');
		$hashPass = sha1(sha1($password.sha1($config['SALT']).$username).$createdDate);
	
		$sql = "
			INSERT INTO {$DATABASE['MAJOR']}._user 
			(email ,	username ,	password 	,IPAddress, 	lastLogin, 	createdDate ,	modifiedDate ,	starusID )
			VALUES
			('{$email}','{$username}','{$hashPass}','{$ipaddress}','0000-00-00',{$createdDate},{$createdDate},1)
		";
		
		$qData = $this->query($sql);
		gotoPage(BASE_PATH.'login');
	}
	


}




?>