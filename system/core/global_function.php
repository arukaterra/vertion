<?php
//global function
function gotoPage($url=null){
	if($url==null) return false;
	echo '<script>window.location="'.$url.'"</script>';
	die();
}
function _p($index=null,$removeBadStrings=true){	
		if($index==null) return false;
		if($removeBadStrings==true) removeBadStrings($_POST[$index]);
		return mysql_escape_string($_POST[$index]);
	
	}	
function removeBadStrings($strings=null){
		if($strings==null) return false;
		$strings = htmlentities($strings);
		return str_replace('-','',(mysql_escape_string(strip_tags(stripslashes($strings)))));
}
function pr($namespace=null){
	print '<pre>';
	print_r($namespace);
	print '</pre>';
}
?>