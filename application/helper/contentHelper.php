<?php 

class contentHelper {
	
	
	function __construct($apps=false){  
		global $config,$DATABASE;
		if($apps) $this->apps = $apps;
	
		$this->config = $config;
		$this->database = $DATABASE;
		
		if(is_array($this->apps->user)) $this->uid = intval($this->apps->user['id']);	
		
			
	}
	
	
	
	function getStickyAds($start=0,$limit=1){ 
 	
		 
		$qProfiler = "";
		if($this->apps->layname)$qProfiler = " AND userid = {$this->apps->layname} "; 
		if($this->apps->calias)$qProfiler = " AND community = {$this->apps->calias} "; 
		
		
		$sql = "
		SELECT *
		FROM `vertion_content`
		WHERE nstatus = 1 AND sticky = 1 {$qProfiler}
		ORDER BY modifieddate DESC
		LIMIT {$start},{$limit}
		
		";
	// pr($sql);
		$qData = $this->apps->fetch($sql,1);
		
		if($qData) {
		
			$qData = $this->getContentSummary($qData);
			return $qData;
		}else return false;
		
	}
	
	function getTimeline($start=0,$limit=6){
	
	
		$search = strip_tags(_g('s')); 
		if(!$search)$search = strip_tags(_p('s')); 
		$qSearch = "";
		
		if($search){
			$qSearch = " AND ( caption like '%{$search}' OR content like '%{$search}%' ) ";
		}
		
		 
		$qProfiler = "";
		if($this->apps->layname)$qProfiler = " AND userid = {$this->apps->layname} "; 
		if($this->apps->calias)$qProfiler = " AND community = {$this->apps->calias} "; 
		
		$sql = "
		SELECT *
		FROM `vertion_content`
		WHERE nstatus = 1 AND sticky = 0 
		{$qSearch} {$qProfiler}
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
		else $vsid = explode('_',$vsid);
		if(!$vsid) return false;
		
		
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
		 	$qData = $this->getContentSummary($qData,true);
			// pr($qData); exit;
			return $qData;
		}else return false;
		
	}
	  
	function getContentSummary($rqData=null,$isDetail=false){
		
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
			$qData[$key]['createddate'] = datereadable($val['createddate']); 
			$qData[$key]['creator'] = array(); 
			$qData[$key]['comment']['total'] = 0;
			$qData[$key]['comment']['users'] = array();
			$qData[$key]['cool']['total'] = 0;
			$qData[$key]['cool']['users'] = array();
			$qData[$key]['cool']['me'] = false; 
			$qData[$key]['views'] = 0;
			$qData[$key]['content'] = stripslashes(nl2br(html_entity_decode($val['content'])));
			$qData[$key]['caption'] = stripslashes(nl2br(html_entity_decode($val['caption'])));
		 
			$qData[$key]['vsid'] = stripslashes(nl2br(html_entity_decode($val['stringid']."_".$val['id']."_".$val['userid']))); 
			 
			$qData[$key]['imagesdata'] = $this->apps->userHelper->getImagesPath($qData[$key],'images','posts');
			 
			
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
			$usersProfile = $this->apps->userHelper->getUserProfile($userSelectedID);
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
								if($this->uid==$valfav['userid'])	$qData[$key]['cool']['me'] = true; 
							 
								$qData[$key]['cool']['users'] = $userfavorites;
								$userfavorites = false;
							 
							}  
						}
					
				}
				if($commentsData){ 
			 
						if($isDetail) $commentsDataComment = $this->getComment($val['id']);
						else $commentsDataComment = $this->getComment($val['id'],false,0,2,true,true);
						
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
									
					$userSelectedID = implode(",",$arrUserid); 
					if(!$userSelectedID) return false;
					$userDetail = $this->apps->userHelper->getUserProfile($userSelectedID);
					 
					if($userDetail){
						  	
							foreach($qData as $key => $val ){						
								if(array_key_exists($val['userid'],$userDetail)){
									$qData[$key]['userdetail'] = $userDetail[$val['userid']]; 
									$data[$val['contentid']][$val['userid']]= $qData[$key];
								}
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

	function getComment($cid=null,$all=false,$start=0,$limit=5,$summary=false,$firstCommentAndLastComment=false){
		// return $cid;
		global $CONFIG;
		if($cid==null) $cid = intval(_p('id'));
		
		if(!$summary) if(intval(_p('start'))>=0)$start = intval(_p('start'));
	
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
						ORDER BY createddate DESC {$qAllRecord}
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
				
				$userSelectedID = implode(",",$arrUserid); 
				if(!$userSelectedID) return false;
				$userDetail = $this->apps->userHelper->getUserProfile($userSelectedID);
					 
				if($userDetail){
				   
				   foreach($qData as $key => $val){
						/* html entity decode */
						$qData[$key]['comment'] = nl2br(html_entity_decode($qData[$key]['comment']));
						$qData[$key]['createddate'] = timeago($qData[$key]['createddate']);
						$arrComment[$val['contentid']][$key] = $qData[$key];
						
						if(array_key_exists($val['userid'],$userDetail)){
							$arrComment[$val['contentid']][$key]['fullname'] = $userDetail[$val['userid']]['fullname'] ; 
							$arrComment[$val['contentid']][$key]['layname'] = $userDetail[$val['userid']]['layname'] ; 
							$arrComment[$val['contentid']][$key]['img'] = $userDetail[$val['userid']]['imagesdata']['image_full_path'] ; 
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
		$community = 0;
		if($this->apps->calias)$community=$this->apps->calias;
		
		if(!$caption) return $data;
		if(!$content)return $data;
		
		if($imagesdata) $images = $imagesdata['arrImage']['filename'];
		 
		$stringid = str_replace(' ','.',$caption).".".strtotime($createddate).".".$this->uid;
		
		
		
		$sql = "
		INSERT INTO  `vertion_content` 
		( `caption`, `content`, `images`, `category`, `type`, `createddate`, `modifieddate`, `userid`, `sticky`, `nstatus`,stringid,community) 
		VALUES 
		(  '{$caption}', '{$content}', '{$images}', '{$category}', '{$type}', '{$createddate}', '{$modifieddate}', '{$userid}', '{$sticky}', '{$nstatus}','{$stringid}',{$community}) 
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