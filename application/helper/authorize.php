<?php
// extends this class if you want authorize on your web
class authorize extends theConstructor {
	
	
	public function __construct() {
		parent::__construct();	
	
		//this just testing login-in,, before must check login on session.. if triggering login then use this authLogin..
		if(methodPost('login')!=1) $this->checkLogin();
		
	} 
	
	function checkLogin(){
		if($this->session->getSession('is_login')==true) return true;
		session_destroy();
		gotoPage(BASE_PATH."login");
		exit;
	
	}
	
	
	

}




?>