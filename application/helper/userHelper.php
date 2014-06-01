<?php 

class userHelper {
	
	
	function __construct($apps=false){  
		global $config,$DATABASE;
		if($apps) $this->apps = $apps;
	
		$this->config = $config;
		$this->database = $DATABASE;
		
		if(is_array($this->apps->user)) $this->uid = intval($this->apps->user['id']);	
	}
	
	function getUserContentID(){
		$layname = false;
		if($this->apps->controller=='profile'){ 
			if($this->apps->func)$layname = strtolower(strip_tags($this->apps->func));
			else $layname=$this->apps->user['username']; 
		}
		
		if(!$layname) return false;
		$sql = "SELECT id FROM _user WHERE username='{$layname}' LIMIT 1 "; 
		$uiddata = $this->apps->fetch($sql);
		
		if($uiddata) return $uiddata['id'];
		
		return false;
		 
	}
	
	function getUserProfile($selectedUserID=null){
		
		if(!$selectedUserID) $selectedUserID= $this->uid;		
		$sql = "SELECT *  FROM _user_profile WHERE userid IN ({$selectedUserID}) ";
		 
		// pr($sql);
		$data = $this->apps->fetch($sql,1);
		if(!$data) return false;
		
		foreach($data as $key => $val){
		 
			$data[$key]['fullname'] =  ucwords(strtolower($data[$key]['name']." ".$data[$key]['lastname']));
			$data[$key]['layname'] =  strtolower($data[$key]['username']); 
			$data[$key]['lastname'] =  ucwords($data[$key]['lastname']); 
			$data[$key]['createddate'] =  datereadable($data[$key]['createddate']); 
			$data[$key]['imagesdata']  = $this->getImagesPath($data[$key],'img','profile');
			$data[$key]['img']  =$data[$key]['imagesdata']['image_full_path'];
			$arrData[$val['id']] = $data[$key];
			
		} 
		
		if(!isset($arrData)) return false;
		return $arrData;
	}
	
	function getImagesPath($thedata=false,$indeximage='image',$imagepath='',$thumbnail='s_'){
		$imagedata['imagepath'] = false;
		$imagedata['imagepath_small'] = false;
		GLOBAL $CONFIG;
		if(is_file(ROOT_PUBLIC_ASSETS_PATH."{$imagepath}/{$thedata[$indeximage]}")) $imagedata['imagepath'] = $imagepath;	 
		if($thumbnail)if(is_file(ROOT_PUBLIC_ASSETS_PATH."{$imagepath}/{$thumbnail}_{$thedata[$indeximage]}")) $imagedata['imagepath_small'] = $imagepath;
  
		if($imagedata['imagepath']){
			$imagedata['image_full_path'] = PUBLIC_ASSETS_PATH.$imagedata['imagepath']."/".$thedata[$indeximage];
			$rootimg = ROOT_PUBLIC_ASSETS_PATH.$imagedata['imagepath']."/".$thedata[$indeximage];
		}else {
			$imagedata['image_full_path'] = PUBLIC_ASSETS_PATH.$imagepath."/default.jpg";
			$rootimg = ROOT_PUBLIC_ASSETS_PATH.$imagepath."/default.jpg";
		}
		if($thumbnail){
			if($imagedata['imagepath_small']) $imagedata['image_full_path_thumb'] = PUBLIC_ASSETS_PATH.$imagedata['imagepath_small']."/{$thumbnail}_".$thedata[$indeximage];
			else $imagedata['image_full_path_thumb'] = PUBLIC_ASSETS_PATH.$imagepath."/default.jpg";
		}
		
		$imagedata['image_type'] = "B";
		
		list($width, $height, $type, $attr) = getimagesize($rootimg);
		/* w : h */
		$w = 0;
		$h = 0;
		if($height>$width) {
			$w = ceil($width/$width); 
			$h = ceil($height/$width); 
			if($w<$h) $imagedata['image_type'] = "P";
		}
		if($width>$height){
		
			$w = ceil($width/$height);
			$h = ceil($height/$height);
			if($w>$h) $imagedata['image_type'] = "L"; 
			
			// $d[$rootimg][] = $rootimg;
			// $d[$rootimg][] = $width;
			// $d[$rootimg][] = $height;
			// $d[$rootimg][] = $w ;
			// $d[$rootimg][] = $h ;
			// $d[$rootimg][] = $imagedata['image_type'];
			// pr($d);
		}
		
		
		// pr($imagedata);
		return $imagedata;
	}
	
	
}




?>