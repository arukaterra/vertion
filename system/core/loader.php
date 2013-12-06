<?php
class Load{

	var $model;
	var $view;
	var $library;
	var $helper;
	
	public function model($modelName){
	if($modelName==null) return false;
	require ROOT_PATH.APPLICATION_PATH.'models/'.$modelName.'.php';
	$$modelName = new $modelName;
	return $$modelName;
	
	}
	
	public function view($viewName=null,$data=null){
		if($viewName==null) return false;
		if($data!=null)
		{
			foreach($data as $key => $val){
			$$key = $val;
		}
		}
		require ROOT_PATH.APPLICATION_PATH.'views/'.$viewName.'.html';
	
	}
	
	public function library($libraryName=null){
	if($libraryName==null) return false;
	require ROOT_PATH.APPLICATION_PATH.'libraries/'.$libraryName.'.php';
	$$libraryName = new $libraryName;
	return $$libraryName;
	
	
	}
	
	public function helper($helperName=null){
		if($helperName==null) return false;
		require ROOT_PATH.APPLICATION_PATH.'helper/'.$helperName.'.php';
		$$helperName = new $helperName;
		return $$helperName;
		
	}




}




?>