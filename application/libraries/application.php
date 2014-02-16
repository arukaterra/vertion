<?php 

class Application extends Load {
	 
	 
	 function initial(){
		parent::__construct(); 
		
		$this->userministrator = $this->library('userministrator');
		
		if($this->load->controller()!='login'){
			
			if(!$this->userministrator->checklogin()){
				//gotoPage(BASE_PATH."login"); 
				exit;
			}
		}
		
		$this->call();
	 }
	
	function call(){
		
		if($this->load->controller()){ 
			$sourcefile = ROOT_PATH.APPLICATION_PATH.APPS_PATH.'/modules/'.$this->load->controller().'.php';
			if(file_exists($sourcefile)){
			 
				include $sourcefile;
			
				$controller = $this->load->controller();
				
				$$controller = new $controller; 
				 	
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
			
		}  
	}
}




?>