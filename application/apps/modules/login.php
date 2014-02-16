<?php

class login extends theConstructor {
	

	public function __construct() {
		parent::__construct();
		
	} 
	
	function index(){
	 
		$data['main_content'] ='frontend/login/login_viewer';
		$this->load->view('frontend/master',$data);
	}
	
	function goLogin(){
	
		$this->AuthLogin();
		
		gotoPage(BASE_PATH."homepage"); 
		exit;
	}
	
	function logout(){
		session_destroy();
	
	}
	
	function AuthLogin(){
			
		//function untuk login
	
		$qData = $this->userministrator->getLoginUser();
		
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
		$data['main_content'] = 'frontend/login/register';
		$this->load->view('frontend/master',$data);
	}
	
	
	function doRegistration(){
		$qData = $this->userministrator->doRegister();
	
	}
}

?>