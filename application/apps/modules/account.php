<?php

class Account extends Authorize{

		
	public function __construct() {
		parent::__construct();
	
	} 
	
	function index(){
			$this->load->view('frontend/profile/profile_view');
	
	}
	
	function logout(){
		session_destroy();
		gotoPage(BASE_PATH."login"); 
	
	
	}


}