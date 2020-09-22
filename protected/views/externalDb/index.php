<?php
if($dataerror) {
    die($dataerror);
}
echo "<center><i>".$count." matches found</i></center><br />\n";
echo "<input type='hidden' id='testsqlcount' value='".$count."' />\n";
if(is_array($data) && !empty($data)) {
    
    $names=$data[0];
    
//Check for duplicate entries

$uniques=array();
$duplicatecount=0;
$duplicates=array();
foreach($data as $key=>$val) {
    $uniqueval=$val['member'].$val['email'];
    if(!in_array($uniqueval, $uniques)) {
        array_push($uniques, $uniqueval);
    } else {
        $duplicatecount++;
        array_push($duplicates, $uniqueval);
    }
}

if($duplicatecount > 0) {
    echo "<center style='color: red'>ERROR: There are ".$duplicatecount." duplicated entries. Your SQL needs to be re-worked</center><br />";    
    echo "<div id='listofduplicates' style='width: 150px; left: auto; right: auto'>";
    foreach($duplicates as $dupe) {
        echo "<li>$dupe</li>";
    }
    echo "</div>";
}

?>
<table class='data'>
  <tr>
<?php
    foreach($names as $key=>$val) {
        echo "<th>$key</th>";    
    }
?>
</tr>
<?php
    foreach($data as $key=>$val) {
        echo "<tr>\n";
        foreach($val as $field=>$value) {
            echo "<td>".$value."</td>\n";
        }
    }  
}
?>
</table>
