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
	
	
	// by bintang
	function upload_image ($file_id, $target_dir, $temp_dir, $filename, $thumb_size_w = 120, $thumb_size_h = 120, $rescale_thumbnail_by_width = true, $no_rescale_thumbnail = false, $max_w = 120, $max_h = 120, $crop_by_max_w = true, $crop_by_max_h = true, $dont_save_thumb = false, $quality = 100 ) {
		if (!isset($_FILES [$file_id])) return false;

		$file_src = $_FILES [$file_id]['tmp_name'];
		$file_type = $_FILES [$file_id]['type'];
		$file_name = $_FILES [$file_id]['name'];
		$file_tmp_dir = $temp_dir;
		$file_dst_dir = $target_dir;
		$file_filename = $filename;
		$rand_value = rand(00000, 99999);
		
		if (!file_exists($file_tmp_dir)) mkdir ($file_tmp_dir, 0644);
		chmod ($file_tmp_dir, 0755);
		
		$file_tmp_fullname = $file_tmp_dir . "tmp-" . $rand_value . "-" . $file_name;
		$file_dst_thumb_fullname = $file_dst_dir . "thumb/" . $file_filename;
		$file_dst_fullname = $file_dst_dir . $file_filename;

		//if (file_exists ($file_tmp_fullname)) chmod ($file_tmp_fullname, 0775);
		$retval = move_uploaded_file($_FILES[$file_id]["tmp_name"], $file_tmp_fullname);
		if (file_exists ($file_tmp_fullname)) {
			//chmod ($file_tmp_fullname, 0775);

			switch ($file_type) {
			case "image/wbmp":
				$im_handler_src = @imagecreatefromwbmp ($file_tmp_fullname);
				break;
			case "image/bmp":
				$im_handler_src = ImageCreateFromBMP ($file_tmp_fullname); // @ custom function
				break;
			case "image/gif":
				$im_handler_src = @imagecreatefromgif ($file_tmp_fullname);
				break;
			case "image/png":
				$im_handler_src = @imagecreatefrompng ($file_tmp_fullname);
				break;
			case "image/x-icon":
				$im_handler_src = @imagecreatefromjpeg ($file_tmp_fullname);
				break;
			case "application/octet-stream":
				$im_handler_src = @imagecreatefromjpeg ($file_tmp_fullname);
				break;
			case "image/jpeg":
				$im_handler_src = @imagecreatefromjpeg ($file_tmp_fullname);
				break;
			default:
				$im_handler_src = false;
			}

			if ($im_handler_src) {
				$src_w = imageSX($im_handler_src);
				$src_h = imageSY($im_handler_src);

				// master image
				$dst_master_w = $src_w;
				$dst_master_h = $src_h;
				$im_master_handler = imagecreatetruecolor($dst_master_w, $dst_master_h);
				
				imagecopyresampled($im_master_handler, $im_handler_src, 0, 0, 0, 0, $dst_master_w, $dst_master_h, $src_w, $src_h);
				
				if (file_exists ($file_dst_fullname)) chmod ($file_dst_fullname, 0775);
				imagejpeg ($im_master_handler, $file_dst_fullname, $quality);
				if (file_exists ($file_dst_fullname)) chmod ($file_dst_fullname, 0775);
				
				imagedestroy($im_master_handler);
				
				// thumbnail image
				if (!$dont_save_thumb) {
					if ($thumb_size_w < 1) $thumb_size_w = 1;
					if ($thumb_size_h < 1) $thumb_size_h = 1;
					if ($no_rescale_thumbnail) {
							$dst_thumb_w = $thumb_size_w;
							$dst_thumb_h = $thumb_size_h;
					}
					else {

						if ($rescale_thumbnail_by_width) {
							$dst_thumb_w = $thumb_size_w;
							$dst_thumb_h = ($thumb_size_w / $src_w) * $src_h;
						}
						else {
							$dst_thumb_w = ($thumb_size_h / $src_h) * $src_w;
							$dst_thumb_h = $thumb_size_h;
						}
					}
					if (($crop_by_max_w) && ($dst_thumb_w > $max_w)) {
						$src_tw = $max_w * $src_w / $dst_thumb_w;
						$dst_thumb_w = $max_w;
					}
					else {
						$src_tw = $src_w;
					}
					if (($crop_by_max_h) && ($dst_thumb_h > $max_h)) {
						$src_th = $max_h * $src_h / $dst_thumb_h;
						$dst_thumb_h = $max_h;
					}
					else {
						$src_th = $src_h;
					}
					
					$im_thumb_handler = imagecreatetruecolor($dst_thumb_w, $dst_thumb_h);
					imagecopyresampled($im_thumb_handler, $im_handler_src, 0, 0, 0, 0, $dst_thumb_w, $dst_thumb_h, $src_tw, $src_th);
					
					if (file_exists ($file_dst_thumb_fullname)) chmod ($file_dst_thumb_fullname, 0775);
					imagejpeg($im_thumb_handler, $file_dst_thumb_fullname, $quality);
					if (file_exists ($file_dst_thumb_fullname)) chmod ($file_dst_thumb_fullname, 0775);
					
					imagedestroy($im_thumb_handler);
				}
				imagedestroy($im_handler_src);
			}
			
			unlink ($file_tmp_fullname);
			return true;
		}
	
		return false;
	}
	
	// by bintang
	// upload image as database (blob)
	function upload_image_as_blob ($file_id, $temp_dir, $quality = 100) {
		if (!isset($_FILES [$file_id])) return false;

		$file_src = $_FILES [$file_id]['tmp_name'];
		$file_type = $_FILES [$file_id]['type'];
		$file_name = $_FILES [$file_id]['name'];
		$file_tmp_dir = $temp_dir;
		$rand_value = rand(00000, 99999);
		
		if (!file_exists($file_tmp_dir)) mkdir ($file_tmp_dir, 0644);
		chmod ($file_tmp_dir, 0644);
		
		$file_tmp_fullname = $file_tmp_dir . "tmp-" . $rand_value . "-" . $file_name;
		
		//if (file_exists ($file_tmp_fullname)) chmod ($file_tmp_fullname, 0775);
		$retval = move_uploaded_file($_FILES[$file_id]["tmp_name"], $file_tmp_fullname);
		if (file_exists ($file_tmp_fullname)) {
			//chmod ($file_tmp_fullname, 0775);

			switch ($file_type) {
			case "image/wbmp":
				$im_handler_src = @imagecreatefromwbmp ($file_tmp_fullname);
				break;
			case "image/bmp":
				$im_handler_src = ImageCreateFromBMP ($file_tmp_fullname); // @ custom function
				break;
			case "image/gif":
				$im_handler_src = @imagecreatefromgif ($file_tmp_fullname);
				break;
			case "image/png":
				$im_handler_src = @imagecreatefrompng ($file_tmp_fullname);
				break;
			case "image/x-icon":
				$im_handler_src = @imagecreatefromjpeg ($file_tmp_fullname);
				break;
			case "application/octet-stream":
				$im_handler_src = @imagecreatefromjpeg ($file_tmp_fullname);
				break;
			case "image/jpeg":
				$im_handler_src = @imagecreatefromjpeg ($file_tmp_fullname);
				break;
			default:
				$im_handler_src = false;
			}

			if ($im_handler_src) {
				$src_w = imageSX($im_handler_src);
				$src_h = imageSY($im_handler_src);

				// master image
				$dst_master_w = $src_w;
				$dst_master_h = $src_h;
				$im_master_handler = imagecreatetruecolor($dst_master_w, $dst_master_h);
				
				imagecopyresampled($im_master_handler, $im_handler_src, 0, 0, 0, 0, $dst_master_w, $dst_master_h, $src_w, $src_h);
				
				// save re-sampled image file
				imagejpeg ($im_master_handler, $file_tmp_fullname . ".jpg", $quality);
				
				imagedestroy($im_master_handler);
				imagedestroy($im_handler_src);
				
				// prepare blob from re-sampled image file
				if (file_exists($file_tmp_fullname . ".jpg")) {
					$blob_data = fread(fopen($file_tmp_fullname,"rb"), filesize($file_tmp_fullname));
					unlink ($file_tmp_fullname . ".jpg");
				}
			}
			
			unlink ($file_tmp_fullname);
			return $blob_data;
		}
		
		return false;
	}
	
	
	// added by bintang
	/*********************************************/
	/* Fonction: ImageCreateFromBMP              */
	/* Author:   DHKold                          */
	/* Contact:  admin@dhkold.com                */
	/* Date:     The 15th of June 2005           */
	/* Version:  2.0B                            */
	/*********************************************/
	
	function ImageCreateFromBMP($filename)
	{
	 //Ouverture du fichier en mode binaire
	   if (! $f1 = fopen($filename,"rb")) return FALSE;
	
	 //1 : Chargement des ent?tes FICHIER
	   $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
	   if ($FILE['file_type'] != 19778) return FALSE;
	
	 //2 : Chargement des ent?tes BMP
	   $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
	                 '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
	                 '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
	   $BMP['colors'] = pow(2,$BMP['bits_per_pixel']);
	   if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
	   $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
	   $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
	   $BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
	   $BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
	   $BMP['decal'] = 4-(4*$BMP['decal']);
	   if ($BMP['decal'] == 4) $BMP['decal'] = 0;
	
	 //3 : Chargement des couleurs de la palette
	   $PALETTE = array();
	   if ($BMP['colors'] < 16777216)
	   {
	    $PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
	   }
	
	 //4 : Cr?ation de l'image
	   $IMG = fread($f1,$BMP['size_bitmap']);
	   $VIDE = chr(0);
	
	   $res = imagecreatetruecolor($BMP['width'],$BMP['height']);
	   $P = 0;
	   $Y = $BMP['height']-1;
	   while ($Y >= 0)
	   {
	    $X=0;
	    while ($X < $BMP['width'])
	    {
	     if ($BMP['bits_per_pixel'] == 24)
	        $COLOR = unpack("V",substr($IMG,$P,3).$VIDE);
	     elseif ($BMP['bits_per_pixel'] == 16)
	     {  
	        $COLOR = unpack("n",substr($IMG,$P,2));
	        $COLOR[1] = $PALETTE[$COLOR[1]+1];
	     }
	     elseif ($BMP['bits_per_pixel'] == 8)
	     {  
	        $COLOR = unpack("n",$VIDE.substr($IMG,$P,1));
	        $COLOR[1] = $PALETTE[$COLOR[1]+1];
	     }
	     elseif ($BMP['bits_per_pixel'] == 4)
	     {
	        $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
	        if (($P*2)%2 == 0) $COLOR[1] = ($COLOR[1] >> 4) ; else $COLOR[1] = ($COLOR[1] & 0x0F);
	        $COLOR[1] = $PALETTE[$COLOR[1]+1];
	     }
	     elseif ($BMP['bits_per_pixel'] == 1)
	     {
	        $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
	        if     (($P*8)%8 == 0) $COLOR[1] =  $COLOR[1]        >>7;
	        elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;
	        elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;
	        elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;
	        elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;
	        elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;
	        elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;
	        elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
	        $COLOR[1] = $PALETTE[$COLOR[1]+1];
	     }
	     else
	        return FALSE;
	     imagesetpixel($res,$X,$Y,$COLOR[1]);
	     $X++;
	     $P += $BMP['bytes_per_pixel'];
	    }
	    $Y--;
	    $P+=$BMP['decal'];
	   }
	
	 //Fermeture du fichier
	   fclose($f1);
	
	 return $res;
	}
	
}
?>
