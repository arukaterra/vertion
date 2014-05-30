<?php

class profile extends application{
	
	function beforFilter($apps=false){
		$this->apps =$apps;
		$this->contentHelper = $this->apps->helper('contentHelper');	
		$this->apps->layname = $this->contentHelper->getUserContentID();
	}
	
	function index(){

	 
		$stickyads = $this->contentHelper->getStickyAds();
		
		 // pr($stickyads);exit;
		$data['stickyads'] = $stickyads;
		$data['qs'] = strip_tags(_g('s'));
		$timeline = $this->contentHelper->getTimeline();
		// pr($timeline);
		 // pr($stickyads);exit;
		$data['timeline'] = $timeline;
		$this->templates('frontend/home/homepage',$data);

	}
	

}

?>

