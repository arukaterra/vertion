<?php

class AdsBlockController extends Load{
	
	var $uri="";
	var $controller="";
	var $func="";
	var $GET="";
	
		
	function uri(){
	$uri = explode('/',$_SERVER['REQUEST_URI']);
	return $uri;
	}
	
	function controller(){
	$segment = $this->uri();
	// pr(count($segment)-1);exit;	
	$this->controller = @$segment[array_search('index.php',$segment)+3];
	return $this->controller;	
	}
	
	function func(){
	$segment = $this->uri();
	
	$this->func = @$segment[array_search('index.php',$segment)+4];
	return $this->func;	
	}
	
	function apps_path(){
	
	}
	
	
}




?>