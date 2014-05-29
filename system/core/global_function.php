<?php
//global function
function gotoPage($url=null){
	if($url==null) return false;
	echo '<script>window.location="'.$url.'"</script>';
	die();
}

function _p($index=null,$removeBadStrings=true){	
		if($index==null) return false;
		if(isset($_POST[$index])){
			if($removeBadStrings==true) removeBadStrings($_POST[$index]);
			return mysql_real_escape_string($_POST[$index]);
		}else return false;
	}	
	
function _g($index=null,$removeBadStrings=true){	
		if($index==null) return false;
		if(isset($_GET[$index])){
			if($removeBadStrings==true) removeBadStrings($_GET[$index]);
			return mysql_real_escape_string($_GET[$index]);
		}else return false;
	}
	
function _r($index=null,$removeBadStrings=true){	
		if($index==null) return false;
		if(isset($_REQUEST[$index])){
			if($removeBadStrings==true) removeBadStrings($_REQUEST[$index]);
			return mysql_real_escape_string($_REQUEST[$index]);
		}else return false;
	}
	
function _html($namespace=null){	
		if($namespace==null) return false;
		return html_entity_decode($namespace); 
}

function removeBadStrings($strings=null){
		if($strings==null) return false;
		$strings = htmlentities($strings);
		return str_replace('-','',(mysql_real_escape_string(strip_tags(stripslashes($strings)))));
}

function pr($namespace=null){
	print '<pre>';
	print_r($namespace);
	print '</pre>';
}

function webcurl($url=null,$params=null){
	if($url==null) return false;
	if($params==null) return false;
	$data_string = http_build_query($params);
	$ch = curl_init($url);                                                                      
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);           
	curl_setopt($ch,CURLOPT_TIMEOUT,$timeout);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);	
	curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$response = curl_exec ($ch);
	$info = curl_getinfo($ch);
	curl_close ($ch);
	return $response;

}


function rankword($str=false,$link=""){
		
		if($str==false)  return false;
		$count = array();
		$arrstr = array();
		$rawstr = array();
		
		$str =  preg_replace("/[^a-zA-Z0-9\s]/", " ", $str);
		$str = preg_replace('/\s+/'," ",$str);		
		
		$rawstr = explode(" ",$str);
		if(count($rawstr)>0){
			foreach($rawstr as $key => $val){
				$str = str_replace(array("\r\n", "\r", "\n")," ",$val);
				$str = preg_replace('/\s+/',"",$str);
				if($str!=''){
					if(strlen($str)>1){
						$arrstr[] = strtoupper($str);
						$count[(string)strtoupper($str)] = 0;
					}
				}
				 
				// $a[$key]['len'] = strlen($str);
			}
			if(count($arrstr)>0){
				foreach($arrstr as $val){
					$count[(string)$val]+=1;
				}
				arsort($count);
				 
				foreach($count as $key => $val){
					if($link) $cloud[] = array("text"=>$key,"weight"=>$val,"html"=>array("class"=>"vertical"),"link"=>array("href"=>"{$link}{$key}"));
					else  $cloud[] = array("text"=>$key,"weight"=>$val,"html"=>array("class"=>"vertical") );
					 
				}
				// pr($cloud);
				$cloud = json_encode($cloud);
				return $cloud;
			}
			return false;
		}
		return false;
}

function timeago($date=false){
		 
		if(!$date)  return $date; 
		  
		$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
		 
		$lengths = array("60","60","24","7","4.35","12","10");
		 
		$now = time();
		 
		$unix_date = strtotime($date);
		 
		// check validity of date
		 
		if(empty($unix_date)) return $date;
		 
		// is it future date or past date
		 
		if($now > $unix_date) { 
			$difference = $now - $unix_date; 
			$tense = "ago"; 
		} else {
			$difference = $unix_date - $now;
			$tense = "from now";
		}
		 
		for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) { 
				$difference /= $lengths[$j]; 
		}
		 
		$difference = round($difference);
		 
		if($difference != 1) $periods[$j].= "s";
		
		return "{$difference} {$periods[$j]} {$tense}";
		 
}

function datereadable($datetimes=false){
	if(!$datetimes)  return $datetimes;  
	$dra = date("M j Y, ",strtotime($datetimes));
	$dra .= date("h:i A",strtotime($datetimes));
	return $dra;
}


?>