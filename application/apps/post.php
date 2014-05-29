<?php

class post extends application {
	function beforFilter($apps=false ){
		$this->apps =$apps;
		 $this->contentHelper = $this->apps->helper('contentHelper');	
		 $this->socialActivityHelper = $this->apps->helper('socialActivityHelper');	
		 $this->uploadHelper = $this->apps->helper('uploadHelper');	
	}
 
	function index(){ 
	   
			
			if (isset($_FILES['images'])&&$_FILES['images']['name']!=NULL) {
				if (isset($_FILES['images'])&&$_FILES['images']['size'] <= 20000000) {
					$path = ROOT_PUBLIC_ASSETS_PATH."posts/";
					$uploaddata = $this->uploadHelper->uploadThisImage($_FILES['images'],$path);
						 
					if ($uploaddata['arrImage']!=NULL) {
						
					} else {
						$uploaddata = false;
					}
				} else {
					$uploaddata = false;
				}
			} else {
				$uploaddata = false;
			}
			
			$res = $this->contentHelper->sendPost($uploaddata);
			 
			print json_encode($res);exit;
	 
		
	}
	
	function detail(){
	
		$res = $this->contentHelper->getDetailPost();
		$typeofdetail = strip_tags(_g('g'));
		
		$data['qs'] = strip_tags(_g('s'));
		if($typeofdetail=='layer'){
			$data['timeline'] = $res;
			// pr($res);
			$this->templates('frontend/post/detail',$data);
		}else{
			print json_encode($res);exit;
		}
	}
	
	function pages(){
		global $locale;
		$respond['result'] = false;
		$respond['code'] = false;
		$respond['message'] = $locale['post']['failed'];
		$respond['data'] =  array();
		 
		$start = intval(_p('start'));
		$timeline = $this->contentHelper->getTimeline($start);
	
		if($timeline){
			$respond['result'] = true;
			$respond['code'] = 1;
			$respond['message'] = $locale['post']['success']; 
			$respond['data'] = $timeline;
		}
		print json_encode($respond);exit;
	}
	
	function comment(){
			$res = $this->socialActivityHelper->sendComment();
			 
			print json_encode($res);exit;
		
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
	
	function cool(){
			$res = $this->socialActivityHelper->sendCool();
			 
			print json_encode($res);exit;
		
	}
	
	function tempimages(){
	
			global $locale; 
			$respond['result'] = false;
			$respond['code'] = false;
			$respond['message'] = $locale['post']['failed'];
			$respond['data'] =  array();
			$path = ROOT_PUBLIC_ASSETS_PATH."temps/";
			
			
			$unlinken = strip_tags(_p('ultm'));
			
			if($unlinken){
				if(unlink($path.$unlinken)){
					$respond['result'] = true;
					$respond['code'] = 1;
					$respond['message'] = $locale['post']['success']; 
					$respond['data'] = array();
				}
			}
			
			if (isset($_FILES['images'])&&$_FILES['images']['name']!=NULL) {
				if (isset($_FILES['images'])&&$_FILES['images']['size'] <= 20000000) {
					
					$uploaddata = $this->uploadHelper->uploadThisImage($_FILES['images'],$path);
						 
					if ($uploaddata['arrImage']!=NULL) {
						
					} else {
						$uploaddata = false;
					}
				} else {
					$uploaddata = false;
				}
			} else {
				$uploaddata = false;
			}
			
			if($uploaddata){
				$respond['result'] = true;
				$respond['code'] = 1;
				$respond['message'] = $locale['post']['success']; 
				$uploaddata['imagesdata'] = 	$this->contentHelper->getImagesPath($uploaddata['arrImage'],'filename','temps',false);
				$respond['data'] = $uploaddata;
			}
			
			print json_encode($respond);exit;
		
	}
}

?>