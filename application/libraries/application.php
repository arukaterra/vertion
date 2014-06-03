<?php 

class Application extends Load {
	 
	var $usersAuth ;
	var $user ;
	var $layname =0;
	var $calias =0;
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
				$this->communityHelper = $this->helper('communityHelper');	
				// pr($this->user);
			}	
		}
		if($this->load->controller()=='share'){
			$this->userHelper 		= $this->helper('userHelper');
			$this->communityHelper 	= $this->helper('communityHelper');	
		}
		
		$this->call();
	 }
	
	function call(){
		 
		if($this->load->controller()){ 
				
			$this->controller = $this->load->controller();
			$this->func =  $this->load->func();
			
			$sourcefile = ROOT_PATH.APPLICATION_PATH.APPS_PATH.'/'.$this->load->controller().'.php';
			if(file_exists($sourcefile)){
				if($this->controller!='post'){
					$this->session->setSession('calias',0);
					$this->session->setSession('layname',0);
					
				}else{
					$this->calias = $this->session->getSession('calias');		
					$this->layname = $this->session->getSession('layname');
				}
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