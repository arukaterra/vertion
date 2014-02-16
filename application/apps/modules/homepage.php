<?php

class homepage extends application {
	
 
	function index(){ 
		 
		$data['name'] = $this->session->getSession('username') ;
		$this->templates('frontend/home/homepage',$data);

	}
	
	

}

?>