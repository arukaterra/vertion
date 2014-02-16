<?php

class Account extends application{
function beforFilter($apps=false){
		$this->apps =$apps;
	}
 
	function index(){
			$this->templates('frontend/profile/profile_view');
	
	}
	
	function logout(){
		session_destroy();
		gotoPage(BASE_PATH."login"); 
	
	
	}


}