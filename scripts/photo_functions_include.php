<?php
if (!defined("inWeSkateCheck")) { die("Acces respins"); }

//createthumbnail function from PHP-Fusion; modified by me.
function createthumbnail($filetype, $origfile, $thumbfile, $new_w, $new_h, $watermark=false) {
	global $setari;

	if ($filetype == 1) { $origimage = imagecreatefromgif($origfile); }
	elseif ($filetype == 2) { $origimage = imagecreatefromjpeg($origfile); }
	elseif ($filetype == 3) { $origimage = imagecreatefrompng($origfile); }
	
	$old_x = imagesx($origimage);
	$old_y = imagesy($origimage);
	
	if ($old_x > $new_w || $old_y > $new_h) {
		if ($old_x < $old_y) {
			$thumb_w = round(($old_x * $new_h) / $old_y);
			$thumb_h = $new_h;
		} elseif ($old_x > $old_y) {
			$thumb_w = $new_w;
			$thumb_h = round(($old_y * $new_w) / $old_x);
		} else {
			$thumb_w = $new_w;
			$thumb_h = $new_h;
		}
	} else {
		$thumb_w = $old_x;
		$thumb_h = $old_y;
	}
	
	$thumbimage = imagecreatetruecolor($thumb_w,$thumb_h);
	$result = imagecopyresampled($thumbimage, $origimage, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);

	if ($watermark) {
		$w_mark = imagecreatefrompng(BASEDIR."images/".$setari['watermark']);
		$water_width = imagesx($w_mark);
		$water_height = imagesy($w_mark);
		$water_xPos = ($thumb_w-$water_width);
		$water_yPos = ($thumb_h-$water_height);
		$result = imagecopy($thumbimage,$w_mark,$water_xPos,$water_yPos,0,0,$water_width,$water_height);
		imagedestroy($w_mark);
	}
	
	touch($thumbfile);

	if ($filetype == 1) { imagegif($thumbimage, $thumbfile); }
	elseif ($filetype == 2) { imagejpeg($thumbimage, $thumbfile); }
	elseif ($filetype == 3) { imagepng($thumbimage, $thumbfile); }
}

function createFixedThumb($filetype, $origfile, $thumbfile, $new_w, $new_h, $watermark=false) {
	global $setari;	

	if ($filetype == 1) { $origimage = imagecreatefromgif($origfile); }
	elseif ($filetype == 2) { $origimage = imagecreatefromjpeg($origfile); }
	elseif ($filetype == 3) { $origimage = imagecreatefrompng($origfile); }
	
	$old_x = imagesx($origimage);
	$old_y = imagesy($origimage);

	$old_ratio = $old_x / $old_y;
	if ($new_w/$new_h > $old_ratio) {
		$thumb_w = $new_w;
		$thumb_h = $new_w/$old_ratio;
	} else {
		$thumb_w = $new_h*$old_ratio;
		$thumb_h = $new_h;
	}

	$x_mid = ($thumb_w-$new_w)/2;
	$y_mid = ($thumb_h-$new_h)/2;

	$process = imagecreatetruecolor($thumb_w,$thumb_h);
	$result = imagecopyresampled($process, $origimage, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);
	$thumbimage = imagecreatetruecolor($new_w,$new_h);
	$result = imagecopyresampled($thumbimage, $process, 0, 0, $x_mid, $y_mid, $new_w, $new_h, $new_w, $new_h);
	imagedestroy($process);
	if ($watermark) {
		$w_mark = imagecreatefrompng(BASEDIR."images/".$setari['watermark']);
		$water_width = imagesx($w_mark);
		$water_height = imagesy($w_mark);
		$water_xPos = ($new_w-$water_width);
		$water_yPos = ($new_h-$water_height);
		$result = imagecopy($thumbimage,$w_mark,$water_xPos,$water_yPos,0,0,$water_width,$water_height);
		imagedestroy($w_mark);
	}
	touch($thumbfile);

	if ($filetype == 1) { imagegif($thumbimage, $thumbfile); }
	elseif ($filetype == 2) { imagejpeg($thumbimage, $thumbfile); }
	elseif ($filetype == 3) { imagepng($thumbimage, $thumbfile); }
}

//image_exists function from PHP-Fusion:
function image_exists($dir, $image) {
	$i = 1;
	$image_name = substr($image, 0, strrpos($image, "."));
	$image_ext = strrchr($image, ".");
	while (file_exists($dir.$image)) {
		$image = $image_name."_".$i.$image_ext;
		$i++;
	}
	return $image;
}
?>
