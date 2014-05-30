<?php

class setting extends application{
	
	function beforFilter($apps=false){
		$this->apps =$apps;
		 $this->contentHelper = $this->apps->helper('contentHelper');	
	}
	
	function index(){

		$data['something'] = '';
		$this->templates('frontend/setting/profile',$data);

	}
	
	function profile(){
		$data['something'] = '';
		$this->templates('frontend/setting/profile',$data);
	}
	
	function account(){
		$data['something'] = '';
		$this->templates('frontend/setting/account',$data);
	}
}

?>

