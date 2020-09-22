<?php
    $themepath = dirname(__FILE__);
    $datapath = $themepath."/links.ctk";
    //ini_set('display_errors', "1");
    //error_reporting(E_ALL);
    $url=$_GET['URL'];
    
    //Write file data
    if(isset($_GET['nid']) && isset($_GET['rid']) && $_GET['rid'] != '{RID}') {
        $handle=fopen($datapath, "a");
        $string=date("U").":".$_GET['nid'].":".$_GET['rid'].":".$url.";";
        fwrite($handle, $string);
        fclose($handle);
    }
    if (!strpos($url, "http://") && !strpos($url, "https://")) {
        $url = "http://".$url;
    }
    
    header( 'location: '.$url ) ;    
  
?>
