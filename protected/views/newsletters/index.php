<?php
/* @var $this NewslettersController */
/* @var $dataProvider CActiveDataProvider */

$baseUrl=Yii::app()->baseUrl;
Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/CListFilter.js');

$this->breadcrumbs=array(
	'Newsletters',
);

$this->menu=array(
	array('label'=>'Create a Newsletter', 'url'=>array('create'), 'visible'=>Yii::app()->user->canCreate),
	array('label'=>'Manage Newsletters', 'url'=>array('admin'), 'visible'=>Yii::app()->user->canCreate),
);

$basedir=Yii::app()->basePath;
$dbfail=$basedir."/../tmp/dbfailure.ctk";

if(file_exists($dbfail)) {
    ?>
    <div style='text-align: center; '>
        <span style='color: red; font-size: 14pt'>--== Email Distribution Currently Suspended ==--</span><br />
        Visit <?php echo CHtml::link('Admin', array('site/admin')) ?> to view reason and restart.
    </div>
    <?php
}
?>
<input type='hidden' id='archiveIDlist' value='' />

<h1>Newsletters
<?php if($user) {
    echo "($user)";
}
?>
</h1>
<div class='listPage'>
    <p class='pageTitle'>Pending Newsletters</p>
    <div class='pageContent'>
<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
    'sortableAttributes'=>array(
        'title',
        'created',
        'sendDate',
    ),
)); ?>
    </div>
</div>
<br />
<div class='listPage'>
    <p class='pageTitle'>Queued Newsletters</p>
    <div class='pageContent'>
<?php $this->widget('zii.widgets.CListView', array(
    'dataProvider'=>$queuedDataProvider,
    'itemView'=>'_view',
    'sortableAttributes'=>array(
        'title',
        'created',
        'sendDate',
    ),
)); ?>
    </div>
</div>
<br />
<div class='listPage'>
    <p class='pageTitle'>Completed
    <button id='bulkArchiveButton' class='hidden' style='display: none; font-size: 1em; border-radius: 5px; color: #009999; background-color: #a2e2dc; float: right; margin-top: 80px; margin-right: -130px' onClick='bulkArchive()'>Bulk Archive</button>
    <button id='checkAllArchiveButtons' class='hidden' style='font-size: 0.6em; border-radius: 5px; color: #009999; background-color: #a2e2dc; float: right; margin-top: 110px; margin-right: -130px' onClick='checkAllArchives()'>Check all</button>
    <?php
echo CHtml::beginForm(CHtml::normalizeUrl(array('Newsletters/index')), 
                      'get', 
                      array('id'=>'filter-form')
                      )
    . CHtml::textField('string', (isset($_GET['string'])) ? $_GET['string'] : '', array('id'=>'string', 'style'=>'width: 250px'))
    . "&nbsp;"
    . CHtml::endForm();        

?>
    <div class='pageContent'>
<?php 


$this->widget('zii.widgets.CListView', array(
    'dataProvider'=>$recentDataProvider,
    'itemView'=>'_view',
    'sortableAttributes'=>array(
        'title',
        'sendDate',
        'id',
    ),
    'id'=>'ajaxListView',
)); ?>
    </div>
</div>

<div class='listPage'>
    <p class='pageTitle'>Archived
<?php
echo CHtml::beginForm(CHtml::normalizeUrl(array('Newsletters/index')),
                        'get',
                        array('id'=>'filter-form2')
                        )
     . CHtml::textField('string', (isset($_GET['string'])) ? $_GET['string'] : '', array('id'=>'string', 'style'=>'width: 250px'))
     . "&nbsp;"
     . CHtml::endForm();
?>
    <div class='pageContent'>
<?php

$this->widget('zii.widgets.CListView', array(
    'dataProvider'=>$archivedDataProvider,
    'itemView'=>'_view',
    'sortableAttributes'=>array(
        'title',
        'sendDate',
    ),
    'id'=>'ajaxListView2'
)); ?>
    </div>
    </p>
</div>

<?php
/**
* FOLLOWING DIV IS POPUP DIALOG     
*/    
?>
<div id='preview-dialog' title='Newsletter Preview'>
    <div id='preview_results'>
    </div>
</div>
<div id='testsql' title='Test SQL'>
    <div id='testsql_results' class='testsql'>
    </div>
</div>
<input type='hidden' id='test_sql' />
<div id='queue-dialog' title='Queue for Delivery'>
    <div id='queue_results'>
    </div>
</div>

<?php
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
?>