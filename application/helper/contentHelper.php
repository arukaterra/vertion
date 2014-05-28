<?php 

class contentHelper {
	
	
	function __construct($apps=false){  
		global $config,$DATABASE;
		if($apps) $this->apps = $apps;
	
		$this->config = $config;
		$this->database = $DATABASE;
		
		if(is_array($this->apps->user)) $this->uid = intval($this->apps->user['id']);	
	}
	
	function getStickyAds($start=0,$limit=6){ 
 
		$sql = "
		SELECT *
		FROM `vertion_content`
		WHERE nstatus = 1 AND sticky = 1 
		ORDER BY modifieddate DESC
		LIMIT {$start},{$limit}
		
		";
 
		$qData = $this->apps->fetch($sql,1);
		
		if($qData) {
		
			$qData = $this->getContentSummary($qData);
			return $qData;
		}else return false;
		
	}
	
	function getTimeline($start=0,$limit=6){
	
	
		$search = strip_tags(_g('s')); 
		
		$qSearch = "";
		
		if($search){
			$qSearch = " AND ( caption like '%{$search}' OR content like '%{$search}%' ) ";
		}
		
		$sql = "
		SELECT *
		FROM `vertion_content`
		WHERE nstatus = 1 AND sticky = 0 
		{$qSearch}
		ORDER BY modifieddate DESC
		LIMIT {$start},{$limit} 
		";
		
		// pr($sql);
		$qData = $this->apps->fetch($sql,1);
		
		if($qData) {
		 	$qData = $this->getContentSummary($qData);
			// pr($qData);exit;
			return $qData;
		}else return false;
		
	}
	
	function getDetailPost($vsid=false){
	
		if(!$vsid)$vsid = explode('_',strip_tags(_g('vsid')));
		
		if(!$vsid) return false;
		$vsid = explode('_',$vsid);
		
		$stringid = $vsid[0];
		$id = $vsid[1];
		$userid = $vsid[2];
	
		
		$sql = "
		SELECT *
		FROM `vertion_content`
		WHERE nstatus = 1 AND stringid = '{$stringid}' AND id = {$id} AND userid={$userid}
		ORDER BY modifieddate DESC
		LIMIT 1
		
		";
	// pr($sql);
		$qData = $this->apps->fetch($sql,1);
		
		if($qData) {
		 	$qData = $this->getContentSummary($qData);
			return $qData;
		}else return false;
		
	}
	  
	function getContentSummary($rqData=null){
		
		if($rqData==null) return array(); 
		$arrayUserID = array(); 
		$userSelectedID = false; 	 
		$usersProfile = array(); 		
		$qData = array();
		$arrayContentID = array();
	 
		foreach($rqData as $key => $val){
			$qData[$key]['imagesdata'] = array();
			$arrayContentID[] = $val['id']; 
			$arrayCategoryID[] = $val['category']; 
			$arrayTypeID[] = $val['type']; 
			//get profile array
			$arrayUserID[$val['userid']] = $val['userid']; 
			$qData[$key] = $val;
			$qData[$key]['ts'] = strtotime($val['modifieddate']);
			$qData[$key]['creator'] = array(); 
			$qData[$key]['comment']['total'] = 0;
			$qData[$key]['comment']['users'] = array();
			$qData[$key]['cool']['total'] = 0;
			$qData[$key]['cool']['users'] = array();
			$qData[$key]['views'] = 0;
		 
			$qData[$key]['vsid'] = $val['stringid']."_".$val['id']."_".$val['userid']; 
			 
			$qData[$key]['imagesdata'] = $this->getImagesPath($qData[$key],'images','posts');
			 
			
		}		
		
		if(!$arrayContentID) return array();
		$selectedContentID = implode(",",$arrayContentID);	
		$selectedCategoryID = implode(",",$arrayCategoryID);		
		$selectedTypeID = implode(",",$arrayTypeID);	
		
		// category data
		$getCategory = $this->getCategory($selectedCategoryID);	
		 		
		// type data
		$getTypeContent = $this->getTypeContent($selectedTypeID);	
		 
		//get profile
		if($arrayUserID){
			$userSelectedID = implode(",",$arrayUserID);
			$usersProfile = $this->getUserProfile($userSelectedID );
		}  
		
		// favorite or like data
		$favoriteData = $this->getFavorite($selectedContentID);
		//comment di list article 
		$commentsData = $this->getComment($selectedContentID,true); 
	 
		// get views
		$getTotalViewsArticle = $this->getTotalViewsArticle($selectedContentID);
		foreach($qData as $key => $val){
				if($getCategory) if(array_key_exists($val['category'],$getCategory)) $qData[$key]['category_name'] = $getCategory[$val['category']];
				if($getTypeContent) if(array_key_exists($val['type'],$getTypeContent)) $qData[$key]['type_name'] = $getTypeContent[$val['type']];
				//user profile
				if($usersProfile) if(array_key_exists($val['userid'],$usersProfile)) $qData[$key]['creator'] = $usersProfile[$val['userid']];
				if($getTotalViewsArticle) if(array_key_exists($val['id'],$getTotalViewsArticle)) $qData[$key]['views'] = $getTotalViewsArticle[$val['id']];
				if($favoriteData){ 
						if(array_key_exists($val['id'],$favoriteData)) $qData[$key]['cool']['total'] = $favoriteData[$val['id']]['total']; 
						if(array_key_exists($val['id'],$favoriteData)) {
							foreach($favoriteData[$val['id']]['users'] as $valfav){
								$userfavorites[] = $valfav; 
								$qData[$key]['cool']['users'] = $userfavorites;
								$userfavorites = false;
							 
							}  
						}
					
				}
				if($commentsData){ 
			 
						$commentsDataComment = $this->getComment($val['id'],false,0,2,true,true);
						
						if(array_key_exists($val['id'],$commentsData)) $qData[$key]['comment']['total'] = $commentsData[$val['id']];
						if($commentsDataComment) {							
							if(array_key_exists($val['id'],$commentsDataComment)) {
								foreach($commentsDataComment[$val['id']] as $valcom){
									$commentarray[] =$valcom;
								}
								$qData[$key]['comment']['users']=$commentarray;
								$commentarray = false;
								 
							}
						} 
				} 
				
		} 
		if($qData) {
			return $qData;
		} else {
		return false;
		} 
	}
	
	 function getUserProfile($selectedUserID=null){
		  
		 $sql = "SELECT id, name, lastname ,img  FROM _user_profile WHERE userid IN ({$selectedUserID}) ";
		 
		// pr($sql);
		$data = $this->apps->fetch($sql,1);
		if(!$data) return false;
		
		foreach($data as $key => $val){
		 
			$data[$key]['fullname'] =  ucwords(strtolower($data[$key]['name']." ".$data[$key]['lastname']));
			$data[$key]['lastname'] =  ucwords($data[$key]['lastname']); 
			$arrData[$val['id']] = $data[$key];
			
		} 
		
		if(!isset($arrData)) return false;
		return $arrData;
	}
	
	function getFavorite($selectedContentID=null){
		global $CONFIG;
		if($selectedContentID==null) $selectedContentID = intval(_p('cid'));
		if($selectedContentID){
			$cidin = " AND contentid IN ({$selectedContentID}) ";
		}
			$sql ="
			SELECT  contentid,userid FROM vertion_cool_statistic WHERE nstatus=  1 {$cidin}  
			";
			$qData = $this->apps->fetch($sql,1);
			if($qData) { 
					foreach($qData as $val){ 
						$arrUserid[$val['userid']] = $val['userid'];	
					}
									
					$users = implode(",",$arrUserid); 
					
					$sql = "SELECT id,name,lastname FROM _user_profile WHERE userid IN ({$users})    ";
					$qDataUser = $this->apps->fetch($sql,1);
					if($qDataUser){
								
						foreach($qDataUser as $val){
							$userDetail[$val['id']]['fullname'] =  ucwords(strtolower($val['name']." ".$val['lastname'])); 
						}
						
						foreach($qData as $key => $val ){						
							if(array_key_exists($val['userid'],$userDetail)) $qData[$key]['userdetail'] = $userDetail[$val['userid']];
							else $qData[$key]['userdetail'] = false;
							$data[$val['contentid']][$val['userid']]= $qData[$key];
						}
						
					
						if($data){
							foreach($data as $key => $val){
								$favoriteData[$key]['total']=count($val);								
								$favoriteData[$key]['users']=$val; 
								
								
							}
						} 
							return $favoriteData;
						}
				}
		return false;
			
			
	}

	function getComment($cid=null,$all=false,$start=0,$limit=50,$summary=false,$firstCommentAndLastComment=false){
		// return $cid;
		global $CONFIG;
		if($cid==null) $cid = intval(_r('id'));
		
		if(!$summary) if(intval(_r('start'))>=0)$start = intval(_r('start'));
	
		if($cid){			
			if($all==true) $qAllRecord = "";
			else  $qAllRecord = " LIMIT {$start},{$limit} ";
			if($all==true) $qFieldRecord = " count(*) total , contentid ";
			else  $qFieldRecord = " * ";
			if($all==true) $qGroupRecord = " GROUP BY contentid ";
			else  $qGroupRecord = "  ";
			
			if($firstCommentAndLastComment) {
				$sql ="	SELECT {$qFieldRecord} 
						FROM vertion_comment 
						WHERE contentid IN ({$cid}) AND nstatus = 1
						{$qGroupRecord}
						ORDER BY createddate ASC LIMIT 1
						";
				 
				$qData[] = $this->apps->fetch($sql);
				
				$sql ="	SELECT {$qFieldRecord} 
						FROM vertion_comment 
						WHERE contentid IN ({$cid}) AND nstatus = 1
						{$qGroupRecord}
						ORDER BY createddate DESC LIMIT 1
						";
				
				$qData[] = $this->apps->fetch($sql);
				 
			}else{
				$sql ="	SELECT {$qFieldRecord} 
						FROM vertion_comment 
						WHERE contentid IN ({$cid}) AND nstatus = 1
						{$qGroupRecord}
						ORDER BY createddate ASC {$qAllRecord}
						";
				
				$qData = $this->apps->fetch($sql,1);
			}
			// pr($firstCommentAndLastComment); 
			// pr($sql); 
			if($qData) {
			
				if($all==true) {
					foreach($qData as $val){
						$arrComment[$val['contentid']] = $val['total'];
					}
					return $arrComment;
				}
				
				
				foreach($qData as $val){
					$arrUserid[$val['userid']] = $val['userid'];				
				}
				
				$users = implode(",",$arrUserid);
				
				$sql = "SELECT id,name,lastname FROM _user_profile WHERE userid IN ({$users})    ";
				$qDataUser = $this->apps->fetch($sql,1);
				// if($firstCommentAndLastComment)  pr($sql); 
				if($qDataUser){
				 
					foreach($qDataUser as $val){
						$userDetail[$val['id']]['fullname'] =  ucwords(strtolower($val['name']." ".$val['lastname'])); 			 
					}
					
					foreach($qData as $key => $val){
						/* html entity decode */
						$qData[$key]['comment'] = html_entity_decode($qData[$key]['comment']);
						$arrComment[$val['contentid']][$key] = $qData[$key];
						
						if(array_key_exists($val['userid'],$userDetail)){
							$arrComment[$val['contentid']][$key]['fullname'] = $userDetail[$val['userid']]['fullname'] ; 
						}
					}
				
					$qData = null;
					// pr($arrComment); 
					return $arrComment;
				}
			}			
		}
		return false;	
	}	
	
	function getTotalViewsArticle($cid=null){
		if($cid==null) return false;
		
		$sql = "SELECT COUNT(*) total,contentid FROM vertion_view_statistic WHERE contentid IN ({$cid}) GROUP BY contentid";
		// pr($sql);
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		$arrViewArticle = false;
		foreach($qData as $key => $val){
			$arrViewArticle[$val['contentid']] = $val['total'];
		}
		if($arrViewArticle){
			return $arrViewArticle;
		}else return false;
		
	}
	function getCategory($categoryid=null){
		if($categoryid==null) return false;
		
		$sql = "SELECT id,categoryname FROM tbl_content_category WHERE id IN ({$categoryid})  ";
		// pr($sql);
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		$arrcategory = false;
		foreach($qData as $key => $val){
			$arrcategory[$val['id']] = $val['categoryname'];
		}
		if($arrcategory){
			return $arrcategory;
		}else return false;
		
	}
	function getTypeContent($typeid=null){
		if($typeid==null) return false;
		
		$sql = "SELECT id,typename FROM tbl_content_type WHERE id IN ({$typeid})  ";
		// pr($sql);
		$qData = $this->apps->fetch($sql,1);
		if(!$qData) return false;
		$arrtype = false;
		foreach($qData as $key => $val){
			$arrtype[$val['id']] = $val['typename'];
		}
		if($arrtype){
			return $arrtype;
		}else return false;
		
	}
	
	function getImagesPath($thedata=false,$indeximage='image',$imagepath='',$thumbnail='small_'){
		$imagedata['imagepath'] = false;
		$imagedata['imagepath_small'] = false;
		GLOBAL $CONFIG;
		if(is_file(ROOT_PUBLIC_ASSETS_PATH."{$imagepath}/{$thedata[$indeximage]}")) $imagedata['imagepath'] = $imagepath;	 
		if(is_file(ROOT_PUBLIC_ASSETS_PATH."{$imagepath}/{$thumbnail}_{$thedata[$indeximage]}")) $imagedata['imagepath_small'] = $imagepath;
 
		
		if($imagedata['imagepath']) $imagedata['image_full_path'] = PUBLIC_ASSETS_PATH.$imagedata['imagepath']."/".$thedata[$indeximage];
		else $imagedata['image_full_path'] = PUBLIC_ASSETS_PATH.$imagepath."/default.jpg";
		if($imagedata['imagepath_small']) $imagedata['image_full_path_thumb'] = PUBLIC_ASSETS_PATH.$imagedata['imagepath_small']."/{$thumbnail}_".$thedata[$indeximage];
		else $imagedata['image_full_path_thumb'] = PUBLIC_ASSETS_PATH.$imagepath."/default.jpg";
		
		
		return $imagedata;
	}
	
	function sendPost($imagesdata=false){
		global $locale;
		$respond['result'] = false;
		$respond['code'] = false;
		$respond['message'] = $locale['post']['failed'];
		$respond['data'] =  array();
		 
		$caption = strip_tags(_p('caption'));
		$content = strip_tags(_p('content')); 
		$category =9;
		$type = 1;
		$createddate =  date("Y-m-d H:i:s");
		$modifieddate = date("Y-m-d H:i:s");
		$userid = $this->uid;
		$sticky = 0;
		$nstatus = 1;
		$images = "";
		
		if(!$caption) return $data;
		if(!$content)return $data;
		
		if($imagesdata) $images = $imagesdata['arrImage']['filename'];
		 
		$stringid = str_replace(' ','.',$caption).".".strtotime($createddate).".".$this->uid;
		
		
		
		$sql = "
		INSERT INTO  `vertion_content` 
		( `caption`, `content`, `images`, `category`, `type`, `createddate`, `modifieddate`, `userid`, `sticky`, `nstatus`,stringid) 
		VALUES 
		(  '{$caption}', '{$content}', '{$images}', '{$category}', '{$type}', '{$createddate}', '{$modifieddate}', '{$userid}', '{$sticky}', '{$nstatus}','{$stringid}') 
		";
		// pr($sql);
		$qData = $this->apps->query($sql);
		$lastId = $this->apps->getLastinsertID();
		if($lastId>0) { 
			$vsid = $stringid."_".$lastId."_".$userid;
			 
			
			$respond['result'] = true;
			$respond['code'] = 1;
			$respond['message'] = $locale['post']['success']; 
			$postdata = $this->getDetailPost($vsid);
			$respond['data'] = $postdata[0];
		} 
		
		return $respond;
		
	}
	
}




?>