<?php

class Welcome extends authorize{
	

	public function __construct() {
		parent::__construct();
	
	} 
	
	function index(){

		$this->load->view('welcome_viewer');
	
	}
	
	function login(){
		$this->AuthLogin();
		echo 'please click to <a href="/index.php/welcome/">next</a>';	
		exit;
	}
	
	function logout(){
		session_destroy();
	
	}
	
	

}

?>