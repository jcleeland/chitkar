<?php
/* @var $this NewslettersController */
/* @var $model Newsletters */
$baseUrl=Yii::app()->baseUrl;
Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/readlist.js');

$this->breadcrumbs=array(
	'Newsletters'=>array('index'),
	$model->title,
);

$menuarray[]=array('label'=>'List Newsletters', 'url'=>array('index'));
$menuarray[]=array('label'=>'Create a Newsletter', 'url'=>array('create'), 'visible'=>Yii::app()->user->canCreate);
if($model->completed != 1) {
    $menuarray[]=array('label'=>'Edit this Newsletter', 'url'=>array('update', 'id'=>$model->id), 'visible'=>Yii::app()->user->canCreate);
} else {
    $menuarray[]=array('label'=>'Archive this Newsletter', 'url'=>'#', 'linkOptions'=>array('submit'=>array('archive','id'=>$model->id),'confirm'=>'Archiving an item deletes all the outgoings records (who it was sent to, reads and link uses). Are you sure you want to archive this item now?'), 'visible'=>Yii::app()->user->canCreate);
}

$menuarray[]=array('label'=>'Delete this Newsletter', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Deleting a newsletter also deletes all the related outgoings records for this newsletter. Are you sure you want to delete this item?'), 'visible'=>Yii::app()->user->canDelete);
$menuarray[]=array('label'=>'Manage Newsletters', 'url'=>array('admin'), 'visible'=>Yii::app()->user->canCreate);
$menuarray[]=array('label'=>'Manage this Newsletter', 'url'=>array('newsletters/update', 'id'=>$model->id), 'visible'=>Yii::app()->user->canCreate);
if($model->completed == 1 && $statistics['read'] < $statistics['total']) {
    $unread=$statistics['total']-$statistics['read'];
    $menuarray[]=array('label' => '👉 Nudge slackers', 'url'=>array('newsletters/nudge'), 'linkOptions'=>array('submit'=>array('nudge','id'=>$model->id),'title'=>'Resend to the '.$unread.' recipients who haven\'t opened this','style'=>'border: 1px solid #b0d4e5; background-color: #d9edf7; border-radius: 3px','confirm'=>'This will create a new newsletter with the same content as the original, for anyone on the original list who has not read this newsletter. Continue?'), 'visible'=>Yii::app()->user->canCreate);
}




$this->menu=$menuarray;

?>

<?php if(Yii::app()->user->hasFlash('success')): ?>
    <div class="alert alert-success">
        <?php echo Yii::app()->user->getFlash('success'); ?>
    </div>
<?php endif; ?>

<?php if(Yii::app()->user->hasFlash('info')): ?>
    <div class="alert alert-info">
        <?php echo Yii::app()->user->getFlash('info'); ?>
    </div>
<?php endif; ?>

<h1><?php echo $model->title; ?> (<?php echo $model->id ?>)</h1>

<div class='emailSurrounds'>
    <div class='emailInner'>
        <div class='emailHead'>
            <div class='emailHeadLeft'><b>Sent:</b></div>
            <div class='emailHeadRight'><?php echo date("l, d M Y - g:i a", strtotime($model->sendDate)); ?></div>
            <div style='clear: both'></div>
        </div>
        <div style='padding: 5px'>
            <div class='emailHeadLeft'><b>To:</b></div>
            <div class='emailHeadRight'>Jane Member</div>
            <div style='clear: both'></div>
        </div>
        <?php 
        if($model->queued == 1) {
        ?>            
        <div style='padding: 5px'>
            <div class='emailHeadLeft'><b>Recipients:</b></div>
            <div class='emailHeadRight'><img src='<?php echo $baseUrl ?>/images/view.png' id='<?php echo $model->id ?>' class='showrecipients clickable' /> <?php echo $statistics['sent'] ?> emails sent out of <?php echo $statistics['total']; ?> total</div>
            <div style='clear: both'></div>
            <div class='emailHeadLeft'></div>
            <div class='emailHeadRight' style='border: 1px solid #ddd; display: none' id='recipientlist'>
                <?php if(empty($model->recipientValues)) {
                        echo $model->recipientSql;
                    } else {
                        echo $model->recipientValues;
                    }?>
            </div>
            <div style='clear: both; display: none' id='recipientlistclear'></div>
        </div>
        <?php
        }
        
        if($model->icsContent) {
            //extract relevent info:
            $ICSstart="";
            $ICSend="";
            $ICSuid="";
            $ICSsummary="";
            $ICSorganiser="";
            $ICSdescription="";
            $ICSlocation="";
            $unfoldedContent = preg_replace("/\r\n[ \t]|[\n\r][ \t]/", " ", $model->icsContent);
            //echo "<hr /><pre>".$unfoldedContent."</pre>";
            $lineEnding = strpos($unfoldedContent, "\r\n") === false ? "\n" : "\r\n";
            $lines = explode($lineEnding, $unfoldedContent);
            foreach ($lines as $line) {
                if (strpos($line, 'SUMMARY:') === 0) {
                    $ICSsummary = substr($line, 8);
                } elseif (strpos($line, 'DTSTART;TZID=Australia/Melbourne:') === 0) {
                    $ICSstart = convertICalDateToDateTime(substr($line, 33));
                } elseif (strpos($line, 'DTEND;TZID=Australia/Melbourne:') === 0) {
                    $ICSend = convertICalDateToDateTime(substr($line, 31));                } elseif (strpos($line, 'UID:') === 0) {
                    $ICSuid = substr($line, 4);
                } elseif (strpos($line, 'ORGANIZER;CN=') === 0) {
                    // Extract the organizer's name and email
                    $organizerInfo = str_replace('ORGANIZER;CN=', '', $line);
                    $parts = explode(':mailto:', $organizerInfo);
                    if (count($parts) == 2) {
                        $ICSorganiser = $parts[0]." (".$parts[1].")";
                    }
                } elseif (strpos($line, 'LOCATION:') === 0) {
                    $ICSlocation = substr($line, 9);
                } elseif (strpos($line, 'DESCRIPTION:') === 0) {
                    $ICSdescription = str_replace("\\n", "\n", substr($line, 12));
                }
            }
            ?>
        <div style='padding: 5px'>
            <div class='emailHeadLeft'><b>Meeting File:</b></div>
            <div class='emailHeadRight'>Starts <?= $ICSstart ?>, Ends <?= $ICSend ?>, Summary <?=$ICSsummary ?>, Organiser <?= $ICSorganiser ?></div>
            <div style='clear: both'></div>
        </div>
            <?php
        }
        if($model->trackReads == 1 && $model->queued == 1) {
        ?>
        <div style='padding: 5px'>
            <div class='emailHeadLeft'><b>Reads:</b></div>
            <div class='emailHeadRight'><img src='<?php echo $baseUrl ?>/images/view.png' id='<?php echo $model->id ?>' class='showreadlist clickable' /> <?php echo $statistics['read'] ?> read out of <?php echo $statistics['total']; ?> total (<?php echo $statistics['percentread'] ?>%).
            <span id='showreadtimes' style='display: none; color: #ff7400; cursor: pointer'>[Read times]</span>
            <?php if(Yii::app()->user->isControl) echo CHtml::link('[Outgoings]', array('outgoings/index&newsletterId='.$model->id)) ?></div>
            
            <div style='clear: both'></div>
        </div>
        <div id="chart_div" style="position: absolute; 
                                   display: block; 
                                   width: 750px; 
                                   height: 250px;
                                   padding: 3px;
                                   background-color: #ccc;
                                   z-index: -500; 
                                   margin-left: 110px;">&nbsp;</div>
               
        <div style='display: none; padding: 5px' id='readlist'>
            <div class='emailHeadLeft'><b>Read report:</b></div>
            <div class='emailHeadRight' id='readreport' style='height: 150px; overflow: auto'></div>
            <div style='clear: both'></div>
        </div>
        <?php
        }
        //If administrator:
        
        if($model->trackLinks == 1 && $model->queued == 1) {
        ?>
        <!-- LINKS REPORT -->
        <div style='padding: 5px'>
            <div class='emailHeadLeft'><b>Links:</b></div>
            <div class='emailHeadRight'><img src='<?php echo $baseUrl ?>/images/view.png' id='<?php echo $model->id ?>' class='showlinklist clickable' /> <?php echo $statistics['links'] ?> links clicked (<?php echo $statistics['percentlinked'] ?>%).
            </div>
            <div style='clear: both'></div>
        </div>
        <div style='display: none; padding: 5px' id='linklist'>
            <div class='emailHeadLeft'><b>Link report:</b></div>
            <div class='emailHeadRight' id='linkreport' style='height: 150px; overflow: auto'></div>
            <div style='clear: both'></div>
        </div>
        <?php
        }
        if($model->queued == 1) {
        ?>            
        <!-- FAILURE REPORT -->
        <div style='padding: 5px'>
            <div class='emailHeadLeft'><b>Failures:</b></div>
            <div class='emailHeadRight'><img src='<?php echo $baseUrl ?>/images/view.png' id='<?php echo $model->id ?>' class='showfaillist clickable' /> <?php echo $statistics['failurereport'] ?> emails failed.
            </div>
            <div style='clear: both'></div>
        </div>
        <div style='display: none; padding: 5px' id='faillist'>
            <div class='emailHeadLeft'><b>Fail report:</b></div>
            <div class='emailHeadRight' id='failreport' style='height: 150px; overflow: auto'>
            </div>
            <div style='clear: both'></div>
        </div>        
        <?php    
        }
        ?>
        <div style='padding: 5px'>
            <div class='emailHeadLeft'><b>Settings:</b></div>
            <div class='emailHeadRight'>Track reads = <?php echo $model->trackReads; ?>, Track Links = <?php echo $model->trackLinks ?>, Allow Archiving = <?php echo $model->archive ?></div>
            <div style='clear: both'></div>
        </div>
        <div style='padding: 5px'>
            <div class='emailHeadLeft'><b>Subject:</b></div>
            <div class='emailHeadRight'><?php echo $model->subject; ?></div>
            <div style='clear: both'></div>
        </div>
    </div>
    <div class='emailContent' ><iframe id='emailContent' src='?r=newsletters/contentpreview&id=<?php echo $model->id ?>' width='100%' height='300px'></iframe></div>

    </div>
    <?php if($model->queued == 1 && $statistics['firstq_read_gap_count'] !== null) {
    ?>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([

          ['Minutes until read', 'Number of Readers'],
//          ['1st Qt\n(<?php echo Globals::secondsToTime($statistics['firstq_read_gap'], "abbrev") ?>)', <?php echo $statistics['firstq_read_gap_count'] ?>],
//          ['Median\n(<?php echo Globals::secondsToTime($statistics['median_read_gap'], "abbrev") ?>)', <?php echo $statistics['median_read_gap_count'] ?>],
//          ['3rd Qt\n(<?php echo Globals::secondsToTime($statistics['thirdq_read_gap'], "abbrev") ?>)', <?php echo $statistics['thirdq_read_gap_count'] ?>],
          [<?php echo round($statistics['firstq_read_gap']/60) ?>, <?php echo $statistics['firstq_read_gap_count'] ?>],
          [<?php echo round($statistics['median_read_gap']/60) ?>, <?php echo $statistics['median_read_gap_count'] ?>],
          [<?php echo round($statistics['thirdq_read_gap']/60) ?>, <?php echo $statistics['thirdq_read_gap_count'] ?>],
        ]);
    
        var options = {
            title: 'Speed Reading Profile',
            /* hAxis: {title: 'Time to read'}, */
            vAxis: {title: 'Emails'},
            backgroundColor: 'white',
            curveType: 'function',
            pointSize: 4,
            legend: {position: 'none'},

        };
    
        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
    chart.draw(data, options);

      }  
    </script>    
    <?php    
    } else {
    ?>
        <script type='text/javascript'>
            $('#chart_div').html("Insufficient data");
        </script>
    <?php    
    }
    function convertICalDateToDateTime($icalDate) {
        // Extract the components from the iCalendar date
        $year = substr($icalDate, 0, 4);
        $month = substr($icalDate, 4, 2);
        $day = substr($icalDate, 6, 2);
        $hour = substr($icalDate, 9, 2);
        $minute = substr($icalDate, 11, 2);
        $second = substr($icalDate, 13, 2);
    
        // Construct a date string
        $dateString = "{$year}-{$month}-{$day} {$hour}:{$minute}:{$second}";
        // Convert to Unix timestamp and then to desired format
        $timestamp = strtotime($dateString ); // Append ' UTC' to interpret as UTC time
        if ($timestamp === false) {
            error_log("Failed to convert icalDate to timestamp: $icalDate");
            return $icalDate;
        }
        // Convert the timestamp to 'Y-m-d H:i:s' in the desired timezone
        $convertedDate = date('Y-m-d H:i:s', $timestamp);
        return $convertedDate;
    }
    
    function convertTimezone($dateString, $timezone) {
        $date = new DateTime($dateString, new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone($timezone));
        return $date->format('Y-m-d H:i:s');
    }
    ?>
