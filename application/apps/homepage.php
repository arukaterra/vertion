<?php

class homepage extends application {
	function beforFilter($apps=false){
		$this->apps =$apps;
	}
 
	function index(){ 
		 $users = $this->session->getSession('users');
		 // pr($users);
		$data['nickname'] = $users->nickname;
		$this->templates('frontend/home/homepage',$data);

	}
	
	

}

?>