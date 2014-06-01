<?php
require ROOT_PATH.APPLICATION_PATH.'libraries/application.php';

class Load extends db {
	var $assign;
	function __construct(){
			$this->load = new AdsBlockController ;
			$this->session = new session_controller ;
			 
	}
	function model($modelName){
		if($modelName==null) return false;
		require ROOT_PATH.APPLICATION_PATH.'models/'.$modelName.'.php';
		$this->$modelName = new $modelName($this);
		return $this->$modelName;
	
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
		
		if($this->assign()){
			foreach($this->assign['userssession'] as $key => $val){
					$$key = $val;
			}
		}
		 // pr($users);
        require_once ROOT_PATH.APPLICATION_PATH.'views/'.$viewName.'.html';
		
	}
	
	 
	function library($libraryName=null){
		if($libraryName==null) return false;
		require ROOT_PATH.APPLICATION_PATH.'libraries/'.$libraryName.'.php';
		$this->$libraryName = new $libraryName($this); 
		return $this->$libraryName; 
	}
	
	function helper($helperName=null){
		if($helperName==null) return false;
		require ROOT_PATH.APPLICATION_PATH.'helper/'.$helperName.'.php';
		$this->$helperName = new $helperName($this);
		return $this->$helperName; 
	}


	function templates($main_content='',$data=array()){ 
		
		$data['main_content']= $main_content;
 
		$this->view('frontend/master',$data); 
	
	}
	
	function assign(){
  
		if($this->session->getSession('is_login')){ 
			// pr();exit;
			 $this->assign['userssession']['users']=$this->apps->user;
			 
			return true;			
		}
		return false;
	
	}
}




?>