<?php

######################Core Loader#################################

require ROOT_PATH.SYSTEM_PATH.'core/db.php'; //class DB 
require ROOT_PATH.SYSTEM_PATH.'core/loader.php'; // class Loader
require ROOT_PATH.SYSTEM_PATH.'core/controller.php'; // class controll automate system to detect url path
require ROOT_PATH.SYSTEM_PATH.'core/session_controller.php'; // class session 

#############################################################
//automate system of controller to files
$adsBlockController = new AdsBlockController;
if($adsBlockController->controller()){

	$sourcefile = ROOT_PATH.APPLICATION_PATH.APPS_PATH.'/modules/'.$adsBlockController->controller().'.php';
	if(file_exists($sourcefile)){
		@require $sourcefile;

		$controller = $adsBlockController->controller();
		
		$$controller = new $controller;
		$func = $adsBlockController->func();

			if($func) {
				
				if(method_exists(@$$controller,$func)){
					@$$controller->$func();
					
				}
			}else @$$controller->index();
			
			exit;
	}else {
		print ' cannot find page '; exit;
	}
	
}else{
	gotoPage(BASE_PATH.DEFAULT_CONTROLLER);
}



?>