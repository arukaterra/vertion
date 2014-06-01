<?php

class profile extends application{
	
	function beforFilter($apps=false){
		$this->apps =$apps;
		$this->contentHelper = $this->apps->helper('contentHelper');	
	
		$this->apps->layname = $this->apps->userHelper->getUserContentID();
		
	}
	
	function index(){

		$profile = $this->apps->userHelper->getUserProfile($this->apps->layname);
		$data['profile'] = $profile[$this->apps->layname];
		
		$stickyads = $this->contentHelper->getStickyAds(); 
		
		$data['stickyads'] = $stickyads;
		$data['qs'] = strip_tags(_g('s'));
		$timeline = $this->contentHelper->getTimeline();
		// pr($timeline);
		 // pr($stickyads);exit;
		$data['timeline'] = $timeline;
		 // pr($data);exit;
		$this->templates('frontend/home/homepage',$data);

	}
	

}

?>

