<?php
/**
* 
* Call these functions from anywhere within app using the example syntax:
* 
*    Globals::secondsToTime($str)
* 
*/

class Globals {
    function secondsToTime($seconds, $texttype="long") 
    {
        $days = floor($seconds / 86400);
        $seconds -= ($days * 86400);

        $hours = floor($seconds / 3600);
        $seconds -= ($hours * 3600);

        $minutes = floor($seconds / 60);
        $seconds -= ($minutes * 60);

        if($texttype=="short") {
            $values = array(
                'dy'    => $days,
                'hr'   => $hours,
                'min' => $minutes,
                'sec' => round($seconds)
            );
        } else {
            $values = array(
                'day'    => $days,
                'hour'   => $hours,
                'minute' => $minutes,
                'second' => round($seconds)
            );
        }

        $parts = array();

        if($texttype=="abbrev") {
            $final= ($values['day']) ? $values['day'] : "";
            $final .=  sprintf("%02d", $values['hour']).":".sprintf("%02d", $values['minute']).":".sprintf("%02d", $values['second']);
            return $final;
        }

        foreach ($values as $text => $value) {
            if ($value > 0) {
                $parts[] = $value . ' ' . $text . ($value > 1 ? 's' : '');
            }
        }

        return implode(' ', $parts);
    }

    /**
    * Converts url looking text into actual HTML hyperlinks with href statements
    * 
    * @param mixed $text - The text to search and linkify
    */
    function linkify_links( $text )
    {
        //Hyperlinks URLs that have not got a hyperlink already 
        $text = preg_replace('@(?!(?!.*?<a)[^<]*<\/a>)(?:(?:https?|ftp|file)://|www\.|ftp\.)[-A-‌​Z0-9+&#/%=~_|$?!:,.]*[A-Z0-9+&#/%=~_|$]@i','<a href="\0">\0</a>', $text );
        return $text;
    }

    
    function calculate_median($arr) {
        sort($arr);
        $count = count($arr); //total numbers in array
        $middleval = floor(($count-1)/2); // find the middle value, or the lowest middle value
        if($count % 2) { // odd number, middle is the median
            $median = $arr[$middleval];
        } else { // even number, calculate avg of 2 medians
            $low = $arr[$middleval];
            $high = $arr[$middleval+1];
            $median = (($low+$high)/2);
        }
        return $median;
    }
    
    function calculate_quartiles($arr) {
        sort($arr);
        //print_r($arr); die();
        $count = count($arr);
        $first=null;
        $second=null;
        $third=null;
        $firstcount=null;
        $secondcount=null;
        $thirdleft=null;
        if(count($arr) > 1) {
            $firstcount = round( .25 * ( $count + 1 ) ) - 1;
            $first=$arr[$firstcount];
            $second = ($count % 2 == 0) ? ($arr[($count / 2) - 1] + $arr[$count / 2]) / 2 : $second = $arr[($count + 1) / 2];
            $thirdcount = round( .75 * ( $count + 1 ) ) - 1;
            $third=$arr[$thirdcount];
            $thirdleft=count($arr)-$thirdcount;
            $secondcount=count($arr)-$firstcount-$thirdleft;
        } elseif(count($arr)==1) {
            $second=$arr[0];
            $secondcount=1;
        }        
        return array("first"=>$first, 
                     "second"=>$second, 
                     "third"=>$third, 
                     "firstcount"=>$firstcount, 
                     "secondcount"=>$secondcount,
                     "thirdcount"=>$thirdleft);        
    }
    
}
?>
