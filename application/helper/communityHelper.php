<?php 

class communityHelper {
	
	
	function __construct($apps=false){  
		global $config,$DATABASE;
		if($apps) $this->apps = $apps;
	
		$this->config = $config;
		$this->database = $DATABASE;
		$this->uid = 0;
		if(is_array($this->apps->user)) $this->uid = intval($this->apps->user['id']);	
	}
	
	
	
	function getTotalCoolCommunity($cmtid=false){
			if(!$cmtid) return array();
			
			$sql ="
				SELECT COUNT(1) total ,vc.community 
				FROM vertion_cool_statistic cs
				LEFT JOIN vertion_content vc ON vc.id = cs.contentid
				WHERE cs.nstatus=  1 AND vc.id = cs.contentid AND vc.nstatus =  1 AND vc.community IN ({$cmtid})
				GROUP BY vc.community 
			";
			$qData = $this->apps->fetch($sql,1);
			if($qData) {
				$arrData = array();
				foreach($qData as $val){
					$arrData[$val['community']] = $val['total'];
				}
				return $arrData;
			}
			
			return array();
			
	}
	
	function getCommunity($cmtid=false){
		global $locale;
		$respond['result'] = false;
		$respond['code'] = false;
		$respond['message'] = $locale['post']['failed']; 
		$respond['data'] = array(); 
		
		$calias = false;
		$qProfilerCommunity =false;
		if($this->apps->controller=='profile'){ 
			if($this->apps->func) $calias = strtolower(strip_tags($this->apps->func));			
			if($calias)$qProfilerCommunity = " communityalias = '{$calias}' ";
			else return  $respond ;
		}else{
			if(!$cmtid)$cmtid= intval(_g('cmtid')); 
			if(!$cmtid)return $respond;
			$qProfilerCommunity = " id = {$cmtid} ";
		}
		if(!$qProfilerCommunity) return $respond;
		$sql = "SELECT * FROM vertion_community WHERE {$qProfilerCommunity} LIMIT 1";
		$data = $this->apps->fetch($sql);
		if(!$data) return $respond;
		$cmtid = intval($data['id']);
		if($data['private']){
			if(!$this->uid) return $respond;
			$sql = "SELECT COUNT(1) total FROM _user_community WHERE userid = {$this->uid} AND communityid = {$cmtid} AND nstatus=1 LIMIT 1 ";
			$qData = $this->apps->fetch($sql);
			if(!$qData) return $respond;
			if($qData['total']<=0) return $respond; 
		}
				
		$data['communityname'] =  ucwords(strtolower($data['communityname'])); 
		$data['createddate'] =  datereadable($data['createddate']); 
		$data['imagesdata']  = $this->apps->userHelper->getImagesPath($data,'images','community');
		$data['logodata']  = $this->apps->userHelper->getImagesPath($data,'logo','community');  
		
		$cooldata =  $this->getTotalCoolCommunity($cmtid);  
		$data['cool'] = 0;
		if($cooldata)	$data['cool'] = $cooldata[$cmtid];
		 
		if($data) { 
			$respond['result'] = true;
			$respond['code'] = 1;
			$respond['message'] = $locale['post']['success']; 
			$respond['data'] = $data; 
		} 
		return $respond;
	
	}
	
	 
	
	function addnewcommunity($imagesdata=false,$imageslogodata=false){
		
		global $locale;
		$respond['result'] = false;
		$respond['code'] = false;
		$respond['message'] = $locale['post']['failed']; 
		 $respond['data'] = array();
	   $images = '';
	   if($imagesdata) $images= $imagesdata['arrImage']['filename'];  
	   $logo = '';
	   if($imageslogodata) $logo= $imageslogodata['arrImage']['filename'];
	   if(!$this->uid) return $respond;
	   $communityname= strip_tags(_p('cname')); 
	   
	   $communityalias= str_replace(" ","", ucwords(strtolower($communityname))); 
	   
	   $private= intval(_p('prvcmt')); 
	   $createddate= date("Y-m-d H:i:s"); 
	   $modifieddate= date("Y-m-d H:i:s"); 
	   $description= strip_tags(_p('dsc'));  
		$nstatus = 1;
		
		$sql = " 
		INSERT INTO  `vertion_community` 
		(  `communityname`,`communityalias`, `images`, `private`, `createddate`, `modifieddate`, `description`, `logo`, `nstatus`, creator) 
		VALUES 
		(  '{$communityname}','{$communityalias}', '{$images}', '{$private}', '{$createddate}', '{$modifieddate}', '{$description}', '{$logo}', '{$nstatus}',{$this->uid} ) ";
		 
		$this->apps->query($sql);
		$lastId = $this->apps->getLastinsertID();
		 	
		if($lastId) { 
			$memberstat = 1;
			$cmtid=$lastId;
			$sql = "
			INSERT INTO  `_user_community` 
			( `userid`, `communityid`, `memberstat`, `joindate`, `nstatus`, `description`) 
			VALUES 
			( '{$this->uid}', '{$cmtid}', '{$memberstat}', '{$createddate}', '{$nstatus}', 'admin' )
			";
			$this->apps->query($sql);
			$lastId = $this->apps->getLastinsertID();
		 	
			if($lastId) {
				$respond['result'] = true;
				$respond['code'] = 1;
				$respond['message'] = $locale['post']['success']; 
				$cmtdata = $this->getCommunity($cmtid);
				if($cmtdata['result']) $respond['data'] = $cmtdata['data'];
				
			}else{
				$respond['code'] = 2;
				$respond['message'] = "error create new user on community "; 
			}
		} 
		
		return $respond;
		
	}
	
	
	
	function updatesCommunity($imagesdata=false,$imageslogodata=false){
		global $locale;
		$respond['result'] = false;
		$respond['code'] = false;
		$respond['message'] = $locale['post']['failed']; 
		   
	   $images = '';
	   if($imagesdata) $images= $imagesdata['filename'];  
	   $logo = '';
	   if($imageslogodata) $logo= $imageslogodata['filename'];
	   
	   $cmtid= intval(_p('cmtid')); 
	   
	   if(!$cmtid) return $respond;
	   if(!$this->uid) return $respond;
	   
	   $communityname= strip_tags(_p('cname')); 
	  
	   $private= intval(_p('private')); 
	 
	   $modifieddate= date("Y-m-d H:i:s"); 
	   $description= strip_tags(_p('dsc')); 
	   $nstatus= intval(_p('stat')); 
	  
		if($communityname)$qUpdate['communityname'] = $communityname;
		if($communityname)$qUpdate['images'] = $images; 
		if($communityname)$qUpdate['description'] = $description;
		if($communityname)$qUpdate['logo'] = $logo;
		$qUpdate['modifieddate'] = $modifieddate; 
		$qUpdate['private'] = $private;
		$qUpdate['nstatus'] = $nstatus;
	  
		
		$qUpdateString = implode(',',$qUpdate);
		
		$sql = " 
		UPDATE `vertion_community` 
		SET 
		{$qUpdateString}
		WHERE  `id` = {$cmtid} AND creator = {$this->uid} LIMIT 1";
		 
		$qData = $this->apps->query($sql); 
		if($qData) { 
			$respond['result'] = true;
			$respond['code'] = 1;
			$respond['message'] = $locale['post']['success']; 
		} 
		
		return $respond;
		
	}

	function addusertocommunity(){
	
		
		global $locale;
		$respond['result'] = false;
		$respond['code'] = false;
		$respond['message'] = $locale['post']['failed']; 
		 
		 $cmtid= intval(_p('cmtid')); 
		 $dsc= strip_tags(_p('dsc')); 
		 $mstaf= intval(_p('mstaf')); 
   
		if(!$cmtid) return $respond;
		if(!$this->uid) return $respond;
	   
		if(!$this->uid) return $respond;
		$sql = "SELECT COUNT(1) total,memberstat FROM _user_community WHERE userid = {$this->uid} AND communityid = {$data['id']} AND nstatus=1 LIMIT 1 ";
		$qData = $this->apps->fetch($sql);
		if(!$qData) return $respond;
		if($qData['total']<1) return $respond; 
		
		if(!$mstaf)return $respond; 
		
		$sql = "SELECT id FROM tbl_member_stat_references WHERE nstatus = 1 AND id = {$mstaf} LIMIT 1";
		$qMData = $this->apps->fetch($sql);
		if(!$qMData) return $respond;
		if($qMData['id']<1) return $respond; 
		if($qMData['id']==1) 	if($qData['memberstat']!=1) return $respond; 			
		
		$mstaf = intval($qMData['id']);
		
		$memberstat = $mstaf;
		
		$sql = "
		INSERT INTO  `_user_community` 
		( `userid`, `communityid`, `memberstat`, `joindate`, `nstatus` ,`description`) 
		VALUES 
		( '{$this->uid}', '{$cmtid}', '{$memberstat}', '{$createddate}', '{$nstatus}','{$dsc}') ";
		$this->apps->query($sql);
		$lastId = $this->apps->getLastinsertID();
		
		if($lastId) {
			$respond['result'] = true;
			$respond['code'] = 1;
			$respond['message'] = $locale['post']['success']; 
		} 
		
		return $respond;
	
	}
 
	
}




?>