<?php
//this class to extends many class
class theConstructor {

	public $load;
	public $userministrator;
	
	public function __construct(){
		global $adsBlockController;
		$this->load = $adsBlockController;
		$this->session = new session_controller;
		$this->userministrator = $this->load->library('userministrator');
	

	}



}




?>