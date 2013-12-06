<?php

class homepage extends theConstructor {
	

	public function __construct() {
		parent::__construct();
	
	} 
	
	function index(){
	
	$data['main_content'] = 'frontend/home/homepage';
	$this->load->view('master_view/frontend/master',$data);

	}
	
	

}

?>