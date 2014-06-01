<?php 

class socialActivityHelper {
	
	
	function __construct($apps=false){  
		global $config,$DATABASE;
		if($apps) $this->apps = $apps;
	
		$this->config = $config;
		$this->database = $DATABASE;
		
		if(is_array($this->apps->user)) $this->uid = intval($this->apps->user['id']);	
	}
	
	function checkVSID($vsid=false){
			
		if(!$vsid)$vsid = explode('_',strip_tags(_p('vsid')));
		else $vsid = explode('_',$vsid);
		if(!$vsid) return false;
		
		$stringid = $vsid[0];
		$id = $vsid[1];
		$userid = $vsid[2]; 
		
		$sql = "
		SELECT id
		FROM `vertion_content`
		WHERE nstatus = 1 AND stringid = '{$stringid}' AND id = {$id} AND userid={$userid} 
		LIMIT  1
		"; 
		 
		$qData = $this->apps->fetch($sql);
		if($qData)if(intval($qData['id'])>0) return $qData['id'];
		return false;
	
	}
	
	
	function sendComment($vsid=false){
		global $locale;
		$respond['result'] = false;
		$respond['code'] = false;
		$respond['message'] = $locale['post']['failed'];
		$respond['data'] =  array();
		 
		$message = strip_tags(_p('message')); 
		if(!$message) return $respond; 
		$cid = $this->checkVSID();
		
		if(!$cid) return $respond; 
		$createddate =  date("Y-m-d H:i:s");
		$modifieddate = date("Y-m-d H:i:s");
		
		$sql = "
			INSERT INTO  `vertion_comment` 
			(  `contentid`, `comment`, `userid`, `createddate`, `modifieddate`, `nstatus`) 
			VALUES 
			(  '{$cid}', '{$message}', '{$this->uid}', '{$createddate}', '{$modifieddate}', '1') 
		";
		// pr($sql);
		$qData = $this->apps->query($sql);
		$lastId = $this->apps->getLastinsertID();
		if($lastId>0) {
			
			$postdata =$this->apps->userHelper->getUserProfile($this->uid);   	 
			$postdata[$this->uid]['comment'] = str_replace('\n','<br/>',$message);
			$postdata[$this->uid]['createddate'] = timeago($createddate);
			
			$respond['result'] = true;
			$respond['code'] = 1;
			$respond['message'] = $locale['post']['success'];
			$respond['data'] = $postdata[$this->uid];
		} 
		
		return $respond;
		
	}
	
	 function sendCool($vsid=false){
		global $locale;
		$respond['result'] = false;
		$respond['code'] = false;
		$respond['message'] = $locale['post']['failed']; 
		   
		$cid = $this->checkVSID();
		
		if(!$cid) return $respond; 
		$createddate =  date("Y-m-d H:i:s");
		$modifieddate = date("Y-m-d H:i:s");
		
		$sql = "
			INSERT INTO `vertion_cool_statistic` 
			( `userid`, `contentid`, `createddate`, `modifieddate`, `nstatus`) 
			VALUES 
			( '{$this->uid}', '{$cid}', '{$createddate}', '{$createddate}', '1')
			ON DUPLICATE KEY UPDATE
			nstatus = IF(nstatus=1,0,1)
		"; 
		
		$qData = $this->apps->query($sql);
		// $lastId = $this->apps->getLastinsertID();
		if($qData) {
		 
			$respond['result'] = true;
			$respond['code'] = 1;
			$respond['message'] = $locale['post']['success']; 
		} 
		
		return $respond;
		
	}
	

	
}




?>