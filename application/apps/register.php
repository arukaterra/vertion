<?php

class register extends application {
	
	function beforFilter($apps=false){
		$this->apps =$apps;
	}
	 
	function index(){
	  
		$this->templates('frontend/login/register' );
	}
	 
	function doRegistration(){
		$qData = $this->apps->userministrator->doRegister();
	
	}
}

?>