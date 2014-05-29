<?php

class share extends application {
	function beforFilter($apps=false ){
		$this->apps =$apps;
		 $this->contentHelper = $this->apps->helper('contentHelper');	
		 $this->socialActivityHelper = $this->apps->helper('socialActivityHelper');	
		 $this->uploadHelper = $this->apps->helper('uploadHelper');	
	}
 
	function index(){ 
	   
		$res = $this->contentHelper->getDetailPost();
		$typeofdetail = strip_tags(_g('g'));
		
		$data['qs'] = strip_tags(_g('s'));
		if($typeofdetail=='layer'){
			$data['timeline'] = $res;
			// pr($res);
			$this->templates('frontend/share/detail',$data);
		}else{
			print json_encode($res);exit;
		}
	 
		
	}
	
	function pagecomment(){
	global $locale;
			$respond['result'] = false;
			$respond['code'] = false;
			$respond['message'] = $locale['post']['failed'];
			$respond['data'] =  array();
			$id = $this->socialActivityHelper->checkVSID();
			$comment = $this->contentHelper->getComment($id);
			 
		if($comment){
			$respond['result'] = true;
			$respond['code'] = 1;
			$respond['message'] = $locale['post']['success']; 
			$respond['data'] = $comment[$id];
		}
			print json_encode($respond);exit;
		
	}
	
}

?>