<?php 


class uploadHelper {
	function __construct($apps){
		 
		$this->apps = $apps;
	if(is_array($this->apps->user)) $this->uid = intval($this->apps->user['id']);	
		
			$this->typeImageAccepted = array("image/jpeg","image/jpg","image/png","image/pjpeg","application/octet-stream");
			$this->typeVideoAccepted = array("video/mpeg","video/m4v","video/quicktime");
			$this->typeDocumentAttachment = array("file/xlsx","file/xls","file/doc","file/docx","file/pdf");
		
	}
	
	function uploadThisImage($files=NULL,$path=NULL,$maxSize=1000,$resizeOriginal=false){
		global $CONFIG;
		 
		$arrImageData['filename'] ="";

		if($files==NULL || $path==NULL) return false;
	 
		$type = explode('.',$files['name']);
		$jmlArr = sizeof($type) - 1;
		if(array_key_exists($jmlArr,$type))if($type[$jmlArr]=='images') $type[$jmlArr] = 'jpeg';
		
		$filename = md5($files['name'].rand(1000,9999)).".".$type[$jmlArr]; 

        if(in_array(strtolower($files['type']),$this->typeImageAccepted)) {
			 
			if(move_uploaded_file($files['tmp_name'],$path.$filename)){ 
			
				list($width, $height, $type, $attr) = getimagesize("{$path}{$filename}");
				$maxSize = $maxSize;
 
				$w_small = $width - ($width * 0.5);
				$h_small = $height - ($height * 0.5);
				$w_tiny = $width - ($width * 0.7);
				$h_tiny = $height - ($height * 0.7);
					
				//images drop color depth
				/* todo : create image color depth  */

				
				$this->resizeImage($w_small,$h_small,$path,$filename,$type[$jmlArr],"s_");
				$this->resizeImage($w_tiny,$h_tiny,$path,$filename,$type[$jmlArr],"t_"); 
				$arrImageData['filename'] =$filename;

				 
				return array('result'=>true,'arrImage'=> $arrImageData);
			
			}
		}
		return array('result'=>false,'arrImage'=> false);
	}
	
	function resizeImage($width=0, $height=0,$path='',$filename='',$extensions='',$targetfilename=''){
		if($width==0) return false;
		if($height==0) return false;
		if($path=='') return false;
		if($filename=='') return false;
		if($extensions=='') return false;
		if($targetfilename=='') return false;
		/* todo : create image resizer */ 
		$jpeg_quality = 100;	  
		//count view dimension, size same as x and y
		$targ_w = $width;
		$targ_h = $height;		
		//count image dimension, size progresize from targ_w
		$width  = $x;
		$height = $y; 
		
		$src = 	$path.$filename;
		try{
			$img_r = false;
			$arrJpgFormat = array("jpg","jpeg","pjpeg");
			if(in_array(strtolower($extensions),$arrJpgFormat)) $img_r = imagecreatefromjpeg($src);
			if($extensions=='png' ) $img_r = imagecreatefrompng($src);
			if($extensions=='gif' ) $img_r = imagecreatefromgif($src);
			if(!$img_r) return false;
			$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

			imagecopyresampled($dst_r,$img_r,0,0,$x,$y,	$targ_w,$targ_h,$width,$height);

			// header('Content-type: image/jpeg');
			$arrJpgFormat = array("jpg","jpeg","pjpeg");
			if(in_array(strtolower($extensions),$arrJpgFormat)) imagejpeg($dst_r,$path.$targetfilename,$jpeg_quality);
			if($extensions=='png' ) imagepng($dst_r,$path.$targetfilename);
			if($extensions=='gif' ) imagegif($dst_r,$path.$targetfilename);
			
		}catch (Exception $e){
			return false;
		}
		return true;
	}
	
	function autoCropCenterArea($imageFilename=null,$imageUrl=null,$width=0,$height=0){
		
		if($imageFilename==null||$imageUrl==null) return false;
		if($width==0||$height==0) return false;
		
		global $CONFIG,$ENGINE_PATH;
		$files['source_file'] = $imageFilename;
		$files['url'] = $imageUrl;
		// $files['real_url'] = $CONFIG['LOCAL_PUBLIC_ASSET'];
		$arrFilename = explode('.',$files['source_file']);
		if($files==null) return false;
		
		$jpeg_quality = 50;
		
		//get x, y : phytagoras
		// to get center of view from image variants
		$phyt = sqrt($width*$width +  $height*$height);
		$x = ceil($phyt/4);
		$y = ceil($phyt/4);			
		//count view dimension, size same as x and y
		$targ_w = $x;
		$targ_h = $y;		
		//count image dimension, size progresize from targ_w
		$width  = $x;
		$height = $y;
		
		if($files['source_file']=='') return false;
		
		$src = 	$files['url'].$files['source_file'];
		try{
			$img_r = false;
			$arrJpgFormat = array("jpg","jpeg","pjpeg");
			if(in_array(strtolower($arrFilename[1]),$arrJpgFormat)) $img_r = imagecreatefromjpeg($src);
			if($arrFilename[1]=='png' ) $img_r = imagecreatefrompng($src);
			if($arrFilename[1]=='gif' ) $img_r = imagecreatefromgif($src);
			if(!$img_r) return false;
			$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

			imagecopyresampled($dst_r,$img_r,0,0,$x,$y,	$targ_w,$targ_h,$width,$height);

			// header('Content-type: image/jpeg');
			$arrJpgFormat = array("jpg","jpeg","pjpeg");
			if(in_array(strtolower($arrFilename[1]),$arrJpgFormat)) imagejpeg($dst_r,$files['url']."square".$files['source_file'],$jpeg_quality);
			if($arrFilename[1]=='png' ) imagepng($dst_r,$files['url']."square".$files['source_file']);
			if($arrFilename[1]=='gif' ) imagegif($dst_r,$files['url']."square".$files['source_file']);
			
		}catch (Exception $e){
			return false;
		}
		 
		list($width, $height, $type, $attr) = getimagesize($files['url']."square".$files['source_file']);
		$maxSize = 600;
		if($width>=$maxSize){
			if($width>=$height) {
				$subs = $width - $maxSize;
				$percentageSubs = $subs/$width;
			}
		}
		if($height>=$maxSize) {
			if($height>=$width) {
				$subs = $height - $maxSize;
				$percentageSubs = $subs/$height;
			}
		}
		if(isset($percentageSubs)) {
		 $width = $width - ($width * $percentageSubs);
		 $height =  $height - ($height * $percentageSubs);
		}
		
		$w_small = $width - ($width * 0.5);
		$h_small = $height - ($height * 0.5);
		$w_tiny = $width - ($width * 0.7);
		$h_tiny = $height - ($height * 0.7);
	 
		return $files['source_file'];
	}
}
?>