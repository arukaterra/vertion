<?php

class register extends application {
	
	function beforFilter($apps=false){
		$this->apps =$apps;
	}
	 
	function index(){
		$data['isactivateform']='register';
	   $this->templates('frontend/login/login_viewer'  ,$data);
	}
	 
	function doRegistration(){
		$qData = $this->apps->usersAuth->doRegister();
		// pr($qData);
		gotoPage(BASE_PATH."login"); 
		exit;
	}
}

?>