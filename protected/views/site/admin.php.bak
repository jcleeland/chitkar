<?php
$this->layout='//layouts/column2';
$this->pageTitle=Yii::app()->name . ' - Administration';
$this->breadcrumbs=array(
    'Admin',
);
if(!Yii::app()->user->isControl) {
     echo "<center>You are not authorised to use this page.</center>";
     Yii::app()->end();
}
$this->menu=array(
    array('label'=>'Outgoings', 'url'=>array('outgoings/index')),
    array('label'=>'Users', 'url'=>array('users/index')),
    array('label'=>'Settings', 'url'=>array('settings/index')),
);  
?>
<h1>Chitkar Administration</h1>
<p><strong>Administration functions should only be accessed by experienced administrators, 
since many of these features require knowledge of Chitkar backends.</strong></p>
<h2><?php echo CHtml::link('View Logs', array('site/logs')) ?></h2>
<p>Read the outgoings queue information & the statistics gathering logs.</p>
<h2><?php echo CHtml::link('Unsubscribes', array('unsubscribes/index')); ?></h2>
<p>Manage the unsubscribes table.</p>
<?php if(Yii::app()->user->isAdmin) {
?>
<h2><?php echo CHtml::link('Settings', array('settings/index')) ?></h2>
<p>Adjust Chitkar defaults and other settings</p>
<h2><?php echo CHtml::link('Users', array('users/index')) ?></h2>
<p>Create and edit Chitkar user logins.</p>
<?php    
}
?>
<h2><?php echo CHtml::link('Outgoings', array('outgoings/index')) ?></h2>
<p>View and manage the outgoings list (queued and sent emails).</p>
<h2><?php echo CHtml::link('Statistics', array('statistics/index')) ?></h2>
<p>View and manage the statistics table (daily statistics records). Note this should never really be adjusted, since it is calculated automatically.</p>
<hr />
<h2>Queue Status Information</h2>
<p><?php
$basedir=Yii::app()->basePath;

$queuelock=$basedir."/../tmp/queuelock.txt";

if(file_exists($queuelock)) {
    $locktime=file_get_contents($queuelock);
    if($locktime < time()) {
        echo "<div style='color: green; font-size: 10pt'>No queue lock in place</div>";
        echo "Next outgoing process will commence within 5 minutes (last queue ended ".date("M d, h:i:s a", $locktime).")";
    } else {
        echo "<div style='color: red; font-size: 10pt'>Lock file exists</div>";
        echo "Mail queue processing is locked until ".date("M d, h:i:s a", $locktime);
    }
} else {
    echo "<div style='color: green; font-size: 10pt'>Next queue process within 5 minutes</div>";
}
?></p>
<hr />
<h2>Queue Process Control</h2>
<p><?php
$dbfail=$basedir."/../tmp/dbfailure.ctk";

if(file_exists($dbfail)) {
    echo "<div style='color: red; font-size: 10pt'>Email Distribution Currently Suspended</div>";
    echo "<div style='width: 500px; height: 70px; padding: 3px; font-family: courier; margin-bottom: 3px; overflow: auto; border: 1px solid #ccc'>".file_get_contents($dbfail)."</div>";
    $buttonval="Restart Queue";
    $confirmmsg="Are you sure you want to restart the queue process? If the queue stopped because of an error people may be sent multiple copies of emails.";
} else {
    echo "<div style='color: green; font-size: 10pt'>Email Distribution Currently Active</div>";
    $buttonval="Stop Queue";
    $confirmmsg="Are you sure you want to stop the queue process? Queued but unsent mails will not be sent until it is restarted.";
}


?><input type='button' value='<?php echo $buttonval ?>' onClick='if(confirm("<?php echo $confirmmsg ?>")) {window.open("?r=site/admin&action=togglequeueprocessing", "_self");}'/></p>    
