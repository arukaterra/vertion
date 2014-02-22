<?php

class login extends application {
	
	function beforFilter($apps=false){
		$this->apps =$apps;
	}
	 
	function index(){
	  	$data['isactivateform']='login';
		$this->templates('frontend/login/login_viewer' ,$data);
	}
	
	function goLogin(){
	
		$this->apps->usersAuth->AuthLogin();
		
		gotoPage(BASE_PATH."login"); 
		exit;
	}
	
	 
	 
}

?>