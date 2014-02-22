<?php
class session_controller{

	function setSession($index,$value=0){
		if($index==null) return false;
		if(is_array($index)){
			foreach($index as $key => $val){
				$_SESSION[$key] = $val;			
			}
		} else $_SESSION[$index] = $value;
	
	}
	
	
	function getSession($index=null){
 
		if($index==null) return  $_SESSION;
		if(array_key_exists($index,$_SESSION))	return $_SESSION[$index];
		return false;
	}

}



?>
