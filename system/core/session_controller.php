<?php
class session_controller{

	function setSession($index,$value=0){
		if($index==null) return false;
		if(is_object($index)){
			foreach($index as $key => $val){
				@$_SESSION[$key] = $val;			
			}
		} else @$_SESSION[$index] = $value;
	
	}
	
	
	function getSession($index=null){
		if($index==null) return false;
		return @$_SESSION[$index];
	}

}



?>
