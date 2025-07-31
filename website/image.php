<?php
	// Set the path to the image
	$themepath = dirname(__FILE__);
	$rootpath = $themepath . '/images/';
    $imgurl = filter_input(INPUT_GET, 'imgurl', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $imgpath = $rootpath . $imgurl;
	//$imgpath = $rootpath . filter_input(INPUT_GET, 'imgurl', FILTER_SANITIZE_STRING);
    $datapath = $themepath."/reads.ctk";
    //ini_set('display_errors', "1");
    // Get the mimetype for the file
	if(function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME);  // return mime type ala mimetype extension
        $mime_type = finfo_file($finfo, $imgpath);
        finfo_close($finfo);    
    } elseif(function_exists('mime_content_type')) {
        $mime_type=mime_content_type($imgpath);
    } else {
        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );
        $exp = explode('.',$imgpath);
        $ext = strtolower(array_pop($exp));
        if (array_key_exists($ext, $mime_types)) {
            $mime_type=$mime_types[$ext];
        } else {
            die("Can't determine mime_type for $ext files (full path is '$imgpath')");
        }    
    }
	//echo $finfo;
    //Write file data
    if(isset($_GET['nid']) && isset($_GET['rid']) && $_GET['rid'] != '{RID}') {
        $handle=fopen($datapath, "a");
        $string=date("U").":".$_GET['nid'].":".$_GET['rid'].":".$_GET['imgurl'].";";
        fwrite($handle, $string);
        fclose($handle);
    }
    
    $temp_mime_type=$mime_type;
    if(strpos($temp_mime_type, ";")) {
        $exploded=explode(";", $temp_mime_type);
        foreach($exploded as $expitem) {
            if(strpos($expitem, "image") !== false) {
                $mime_type=$expitem;
            }
        }
    }
    //image/png; charset=binary
    switch ($mime_type){
		case "image/jpeg":
			// Set the content type header - in this case image/jpg
			header('Content-Type: image/jpeg');
			
			// Get image from file
			$img = imagecreatefromjpeg($imgpath);
			
			// Output the image
			imagejpeg($img);
			
			break;
	    case "image/png":
			// Set the content type header - in this case image/png
			header('Content-Type: image/png');
			
			// Get image from file
			$img = imagecreatefrompng($imgpath);
			
	        // integer representation of the color black (rgb: 0,0,0)
	        $background = imagecolorallocate($img, 0, 0, 0);
			
	        // removing the black from the placeholder
	        imagecolortransparent($img, $background);
			
	        // turning off alpha blending (to ensure alpha channel information 
	        // is preserved, rather than removed (blending with the rest of the 
	        // image in the form of black))
	        imagealphablending($img, false);
			
	        // turning on alpha channel information saving (to ensure the full range 
	        // of transparency is preserved)
	        imagesavealpha($img, true);
			
			// Output the image
			imagepng($img);
			
	        break;
		case "image/gif":
			// Set the content type header - in this case image/gif
			header('Content-Type: image/gif');
			
			// Get image from file
			$img = imagecreatefromgif($imgpath);
			
	        // integer representation of the color black (rgb: 0,0,0)
	        $background = imagecolorallocate($img, 0, 0, 0);
			
	        // removing the black from the placeholder
	        imagecolortransparent($img, $background);
			
			// Output the image
			imagegif($img);
			
			break;
        default: 
            echo "Image not available. ($mime_type, $imgurl)";
            break;
	}
	
	// Free up memory
	@imagedestroy($img);
?>