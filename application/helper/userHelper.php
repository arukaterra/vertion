<?php 

class userHelper {
	
	
	function __construct($apps=false){  
		global $config,$DATABASE;
		if($apps) $this->apps = $apps;
	
		$this->config = $config;
		$this->database = $DATABASE;
		$this->uid = 0;
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
		if(!$selectedUserID)  return false;
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
			$communitydata  =$this->getUserCommunity($selectedUserID);
			$data[$key]['community'] = $communitydata[$data[$key]['userid']];
			$arrData[$val['id']] = $data[$key];
			
		} 
		
		if(!isset($arrData)) return false;
		return $arrData;
	}
	
	function getUserCommunity($selectedUserID=false){
			 	if(!$selectedUserID) $selectedUserID= $this->uid;		
			$sql = "
			SELECT uc.communityid ,uc.userid,vc.*
			FROM _user_community uc 
			LEFT JOIN vertion_community vc ON vc.id = uc.communityid
			WHERE 
				uc.userid IN ({$selectedUserID})  
				AND  vc.id = uc.communityid 
				AND uc.nstatus=1 
				AND vc.nstatus=1
			GROUP BY uc.communityid,uc.userid 
			ORDER BY uc.joindate DESC
			";
			$qData = $this->apps->fetch($sql,1);
			if(!$qData)return array();
			$arrData = array();
			foreach($qData as $key => $val){
				$qData[$key]['communityname'] =  ucwords(strtolower($qData[$key]['communityname'])); 
				$qData[$key]['createddate'] =  datereadable($qData[$key]['createddate']); 
				$qData[$key]['imagesdata']  = $this->getImagesPath($qData[$key],'images','community');
				$qData[$key]['logodata']  = $this->getImagesPath($qData[$key],'logo','community');  
				$arrData[$qData[$key]['userid']][] = $qData[$key];				
			}
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
			if($w<$h) $imagedata['image_type'] = "P5";
			if($h>3) $imagedata['image_type'] = "P5";
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
	
	function updateMyProfile($imagesdata=false){
		
		global $locale;
		$respond['result'] = false;
		$respond['code'] = false;
		$respond['message'] = $locale['post']['failed'];
		$respond['data'] =  array();
		 
		$fullname = strip_tags(_p('fullname')); 
		$arrName = explode(' ',$fullname); 
		$aboutme = strip_tags(_p('aboutme')); 
		$dob = strip_tags(_p('dob')); 
		$gender = strip_tags(_p('gender')); 
		$images =false;
		 if($imagesdata) $images = $imagesdata['arrImage']['filename'];
		
		if(array_key_exists(0,$arrName)) $updateProfile['name'] = $arrName[0];
		if(array_key_exists(1,$arrName)) $updateProfile['lastname'] = $arrName[1];
		// if($aboutme) $updateProfile['aboutme'] = $aboutme; 
		if($dob) $updateProfile['birthday'] = $dob; 
		if($gender) $updateProfile['gender'] = $gender; 
		if($images) $updateProfile['img'] = $images; 
		$updateProfile['modifieddate'] =date("Y-m-d H:i:s"); 
		$qUpdateProfile = array();
		foreach($updateProfile as $key =>$val){
			$qUpdateProfile[] = " {$key}='{$val}' ";
		}
		if(!$qUpdateProfile) return $respond;
		$qUpdate = implode(',',$qUpdateProfile);
		$sql = "
				UPDATE  `_user_profile` SET {$qUpdate}
				WHERE userid = {$this->apps->user['id']} LIMIT 1
		"; 
		 
		$qData = $this->apps->query($sql); 
		if($qData) {
		  
			$respond['result'] = true;
			$respond['code'] = 1;
			$respond['message'] = $locale['post']['success']; 
			$respond['data'] =  array();
		} 
		
		return $respond;
		
	}
}




?>