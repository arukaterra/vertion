<?php

class homepage extends application {
	function beforFilter($apps=false){
		$this->apps =$apps;
	}
 
	function index(){ 
		 
		$data['name'] = $this->session->getSession('username') ;
		$this->templates('frontend/home/homepage',$data);

	}
	
	

}

?>