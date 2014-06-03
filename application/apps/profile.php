<?php

class profile extends application{
	
	function beforFilter($apps=false){
		$this->apps =$apps;
	
		$this->contentHelper = $this->apps->helper('contentHelper');	
		
		$communityProfiler = $this->apps->communityHelper->getCommunity();
		// pr($communityProfiler);
		if($communityProfiler) if($communityProfiler['result'])$this->apps->calias = $communityProfiler['data']['id'];
		$this->apps->layname = $this->apps->user['id'];
		 
		if(!$this->apps->calias) $this->apps->layname = $this->apps->userHelper->getUserContentID();
		
		$this->apps->session->setSession('calias',$this->apps->calias);		
		$this->apps->session->setSession('layname',$this->apps->layname);		
	}
	
	function index(){

		$profile = $this->apps->userHelper->getUserProfile($this->apps->layname);
		$data['profile'] = $profile[$this->apps->layname];
		$data['stickyads'] = array();
		$data['timeline'] = array();
		
		if($this->apps->layname){
			$stickyads = $this->contentHelper->getStickyAds(); 
			
			$data['stickyads'] = $stickyads;
			$data['qs'] = strip_tags(_g('s'));
			$timeline = $this->contentHelper->getTimeline();
			// pr($timeline);
			 // pr($stickyads);exit;
			$data['timeline'] = $timeline;
		}
		 // pr($data);exit;
		$this->templates('frontend/home/homepage',$data);

	}
	

}

?>

