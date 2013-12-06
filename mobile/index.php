<?php 
/* before start----------------------- teach this ---------------------------------------------------------
	alway using this constructor.....
	//
		public function __construct() {
		parent::__construct();
		print_r('<pre>');
		print_r($this);
		} 
	//
	just print_r($this) on every controller n you will be know structure of this frame work
	this the structure:
	
	Welcome Object 													Welcome is Controller
	(
		[load] => Load Object										if you wan to load MVLH 
			(														
				[model] => 											ex : $this->load->model('model filename'),etc
				[view] => 											ex : $this->load->view('view filename'),etc
				[library] => 										ex : $this->load->library('library filename'),etc
				[helper] => 										ETC
				[ads] => AdsBlockController Object
					(
						[uri] => 
						[controller] => 
						[func] => 
						[GET] => 
					)

			)

		[userministrator] => Userministrator Object					to access useranimistrator class
			(
				[userID] => 
				[userFullName] => 
				[userSessionKey] => 
				[userIPAddress] => 
				[userISLogin] => 
				[userDB] => _user
			)

		[uri] => 													to get URI, CONTROLLER, n  FUNCTION name
		[controller] => 											ex : $this->controller();
		[func] => 
		[GET] => 
)

	


--------------------------------------------------------END --------------------------------0000000000000-------------*/

require 'config/config.php';
require 'system/settings.php';




?>