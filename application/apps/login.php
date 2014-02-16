<?php

class login extends application {
	
	function beforFilter($apps=false){
		$this->apps =$apps;
	}
	 
	function index(){
	  
		$this->templates('frontend/login/login_viewer' );
	}
	
	function goLogin(){
	
		$this->AuthLogin();
		
		gotoPage(BASE_PATH."homepage"); 
		exit;
	}
	
	 
	function AuthLogin(){
			
		//function untuk login
	 
		$qData = $this->apps->userministrator->getLoginUser();
		
		if($qData)	{
			$this->session->setSession('is_login',true);
			$this->session->setSession($qData);	
		}else{
			$this->session->setSession('is_login',false);
		}
		
		if($this->session->getSession('is_login')==false) gotoPage(BASE_PATH."login"); 
		else  $this->session->setSession('verified',1);

	
	}
	
	
	function register(){
	 
		$this->templates('frontend/login/register' );
	}
	
	
	function doRegistration(){
		$qData = $this->apps->userministrator->doRegister();
	
	}
}

?>