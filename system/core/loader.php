<?php
require ROOT_PATH.APPLICATION_PATH.'libraries/Application.php';

class Load extends db {

	var $model;
	var $view;
	var $library;
	var $helper;
	var $session;
	var $load;
	
	function __construct(){
			$this->load = new AdsBlockController ;
			$this->session = new session_controller ;
	}
	function model($modelName){
		if($modelName==null) return false;
		require ROOT_PATH.APPLICATION_PATH.'models/'.$modelName.'.php';
		$$modelName = new $modelName($this);
		return $$modelName;
	
	}
	
	function view($viewName=null ,$data=null){
		if($viewName==null) return false;
		 
		
		if($data!=null)
		{
			$data = $data;
			foreach($data as $key => $val){
			$$key = $val;
			}
		}
		 
        require_once ROOT_PATH.APPLICATION_PATH.'views/'.$viewName.'.html';
		
	}
	
	 
	function library($libraryName=null){
		if($libraryName==null) return false;
		require ROOT_PATH.APPLICATION_PATH.'libraries/'.$libraryName.'.php';
		$$libraryName = new $libraryName($this);
		return $$libraryName; 
	}
	
	function helper($helperName=null){
		if($helperName==null) return false;
		require ROOT_PATH.APPLICATION_PATH.'helper/'.$helperName.'.php';
		$$helperName = new $helperName($this);
		return $$helperName; 
	}


	function templates($main_content='',$data=array()){ 
		
		$data['main_content']= $main_content;
 
		$this->view('frontend/master',$data); 
	
	}

}




?>