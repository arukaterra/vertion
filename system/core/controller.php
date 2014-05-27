<?php

class AdsBlockController {
	
	var $uri="";
	var $controller="";
	var $func="";
	var $GET="";
	
		
	function uri(){
		 
		$oriuri = $_SERVER['REQUEST_URI']; 
		$dispath  = parse_url($oriuri);
		if(array_key_exists('path',$dispath))	$uri = explode('/',$dispath['path']);
		else $uri = explode('/',$oriuri);
		return $uri;
	}
	
	function controller(){
		$segment = $this->uri();
		// pr(count($segment)-1);exit;	
		$findindex = intval(array_search('index.php',$segment));
		if($findindex==0)$uricontroll = 4;
		else $uricontroll = $findindex+1;
		$this->controller = $segment[$uricontroll];
		return $this->controller;	
	}
	
	function func(){
		$segment = $this->uri(); 
		$findindex = intval(array_search('index.php',$segment));
		// pr($segment);
		// pr($findindex);
		// pr($uricontroll);
		// exit;
		if($findindex==0)$uricontroll = 5;
		else $uricontroll = $findindex+2;
		 if(array_key_exists($uricontroll,$segment)) 	$this->func = $segment[$uricontroll];
		 else 	$this->func = "";
		return $this->func;	
	}
	
	function apps_path(){
	
	}
	
	
}




?>