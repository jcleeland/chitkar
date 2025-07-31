<?php
$baseUrl=Yii::app()->baseUrl;
Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/logs.js');


$this->layout='//layouts/column2';
$this->pageTitle=Yii::app()->name . ' - View Logs';
$this->breadcrumbs=array(
    'View Logs',
);

if(!Yii::app()->user->isControl && !Yii::app()->user->isAdmin) {
     echo "<center>You are not authorised to use this page.</center>";
     Yii::app()->end();
}  
?>
<div class='pageTitle'>Statistics Cron Log</div>
<div class='pageExplain'>Regularly checks website for page view and link use data.</div>
<div style='height: 200px; overflow: auto; font-size: 8pt; background-color: #fffff1' id='statslog'>
    <?php echo str_replace('~', '<br />', htmlspecialchars(str_replace('<br />', '~', $statslog))); ?>
</div>
<b>Stats Log Management: (<?php echo substr_count($statslog, "\n"); ?> lines) </b>
[<?php echo CHtml::link('Trim by half', array('site/trimStatsLog&lines=half'))?>]
[<?php echo CHtml::link('Trim by a quarter', array('site/trimStatsLog&lines=quarter'))?>]
[<?php echo CHtml::link('Trim all but 50 lines', array('site/trimStatsLog&lines=all'))?>]


<br /><br />
<div class='pageTitle'>Email Queue Processing Log</div>
<div class='pageExplain'>Regularly processes unsent queued emails.<br />
- Last queue processing occurred - <?php echo $queuelock['started'] ?><br />
- Queue processing locked until - <?php echo $queuelock['until'] ?><br />
- Next queue process expected at - <?php echo $queuelock['expected'] ?> <br />
- Current server time - <?php echo date("H:i:s, d M Y");  ?></div>
<div style='height: 200px; overflow: auto; font-size: 8pt; background-color: #fffff1' id='queuelog'>
    <?php echo $queuelog; ?>
</div>
<b>Queue Log Management: (<?php echo substr_count($queuelog, "\n"); ?> lines) </b>
[<?php echo CHtml::link('Trim by half', array('site/trimQueueLog&lines=half'))?>]
[<?php echo CHtml::link('Trim by a quarter', array('site/trimQueueLog&lines=quarter'))?>]
[<?php echo CHtml::link('Trim all but 50 lines', array('site/trimQueueLog&lines=all'))?>]

