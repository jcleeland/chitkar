<?php
    $themepath = dirname(__FILE__);
    $datapath = $themepath."/links.ctk";

    ini_set('display_errors', "1");
    error_reporting(E_ALL);
    $url=$_GET['URL'];
    $fixed="";

    // If the URL link ends in a common suffix (like .htm, .html, .pdf, .doc), but has no dot before the suffix, add the dot
    $common_suffixes = array('htm', 'html', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar', 'php', 'asp', 'aspx', 'jsp');
    foreach ($common_suffixes as $suffix) {
        $suffix_length = strlen($suffix);
        //echo "Checking URL ($url) for suffix ($suffix) - $suffix_length long<br />\n";
        //echo "Last $suffix_length characters of URL: " . substr($url, -$suffix_length) . "<br />\n";
        if (strlen($url) > $suffix_length && 
            substr($url, -$suffix_length) === $suffix && 
            substr($url, -($suffix_length + 1), 1) !== '.') {
            //echo "Modifying URL ($url) to add missing dot before suffix ($suffix)\n";
            $url = substr($url, 0, -$suffix_length) . '.' . $suffix;
            $fixed = " [Fixed]";
            break;
        }
    }


  
    
    //Write file data
    if(isset($_GET['nid']) && isset($_GET['rid']) && $_GET['rid'] != '{RID}') {
        $handle=fopen($datapath, "a");
        $string=date("U").":".$_GET['nid'].":".$_GET['rid'].":".$url.$fixed.";";
        fwrite($handle, $string);
        fclose($handle);
    }
    // If URL doesn't already start with http:// or https://, default to https://
    if (stripos($url, 'http://') !== 0 && stripos($url, 'https://') !== 0) {
        $url = 'https://' . $url;
    }
    
    header( 'location: '.$url ) ;    
  
?>
