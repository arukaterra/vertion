<?php 

class Application extends Load {
	 
	var $usersAuth ;
	var $user ;
	var $layname ;
	var $controller ;
	var $func ;
	var $userHelper ;
	
	function __construct(){
		parent::__construct();
		
				
	}
	
	function initial(){ 
		$this->usersAuth = $this->library('usersAuth');	
		if($this->load->controller()!='login'&&$this->load->controller()!='share'&&$this->load->controller()!='register'){
			
			if(!$this->usersAuth->checklogin()){
				gotoPage(BASE_PATH."login"); 
				exit;
			}else{
				$activeuser = @$this->session->getSession('users');
				$this->userHelper = $this->helper('userHelper');	
				$activeuserdata = $this->userHelper->getUserProfile($activeuser['id']); 
				$this->user = $activeuserdata[$activeuser['id']]; 
			}	
		}
		
		$this->call();
	 }
	
	function call(){
		 
		if($this->load->controller()){ 
				
			$this->controller = $this->load->controller();
			$this->func =  $this->load->func();
			
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
							
						}else{
						 
							$$controller->index();
						}
						
				}else $$controller->index();
					
				exit;
			}else {
				
				/* vertion screen name link */
				$this->func = $this->load->controller();
				$this->controller = 'profile';
				
				$sourcefile = ROOT_PATH.APPLICATION_PATH.APPS_PATH.'/'.$this->controller.'.php';
				
				if(file_exists($sourcefile)){
				 
					include $sourcefile;
				
					$controller = $this->controller;
					
					$$controller = new $controller; 
					
					$$controller->beforFilter($this);
					
					$$controller->index(); 
						
					exit;
				}else{
					pr(' cannot find page '); 
					// pr($sourcefile); 
					exit;
				}
			}
			
		}else{
				gotoPage(BASE_PATH.DEFAULT_CONTROLLER); 
				exit;
		}		
	}
}




?>