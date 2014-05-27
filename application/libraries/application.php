<?php 

class Application extends Load {
	 
	var $usersAuth ;
	var $user ;
	
	function __construct(){
		parent::__construct();
		
				
	}
	
	function initial(){ 
		$this->usersAuth = $this->library('usersAuth');	
		if($this->load->controller()!='login'&&$this->load->controller()!='register'){
			
			if(!$this->usersAuth->checklogin()){
				gotoPage(BASE_PATH."login"); 
				exit;
			}else{
				$this->user = @$this->session->getSession('users');
			}	
		}
		
		$this->call();
	 }
	
	function call(){
		 
		if($this->load->controller()){ 
			$sourcefile = ROOT_PATH.APPLICATION_PATH.APPS_PATH.'/'.$this->load->controller().'.php';
			if(file_exists($sourcefile)){
			 
				include $sourcefile;
			
				$controller = $this->load->controller();
				
				$$controller = new $controller; 
				
				$$controller->beforFilter($this);
				
				$func = $this->load->func();
				if($func) {
						
						if(method_exists($$controller,$func)){
							$$controller->$func();
							
						}
				}else $$controller->index();
					
				exit;
			}else {
				pr(' cannot find page '); 
				pr($sourcefile); 
				exit;
			}
			
		}else{
				gotoPage(BASE_PATH.DEFAULT_CONTROLLER); 
				exit;
		}		
	}
}




?>