<?php

class Profile extends Authorize{
	
	public function __construct() {
    parent::__construct();
	
	} 
	
	function index(){

	$data['main_content'] = 'frontend/profile/profile_view';
	$this->load->view('master_view/frontend/master',$data);

	}
	

}

?>

