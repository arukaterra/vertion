<?php

class logout extends application {
	
	function beforFilter($apps=false){
		$this->apps =$apps;
	}
	 
	function index(){
	  	session_destroy();
		 	
		gotoPage(BASE_PATH."login"); 
		exit;
	}
	
	 
}

?>