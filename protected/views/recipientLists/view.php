<?php
/* @var $this RecipientListsController */
/* @var $model RecipientLists */
$baseUrl=Yii::app()->baseUrl;
Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/externalDb.js');
$this->breadcrumbs=array(
	'Recipient Lists'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List of Lists', 'url'=>array('index')),
	array('label'=>'Create List', 'url'=>array('create'), 'visible'=>Yii::app()->user->canCreate),
	array('label'=>'Update this List', 'url'=>array('update', 'id'=>$model->id), 'visible'=>Yii::app()->user->canCreate),
	array('label'=>'Delete this List', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?'), 'visible'=>Yii::app()->user->canDelete),
	array('label'=>'Manage Lists', 'url'=>array('admin'), 'visible'=>Yii::app()->user->canCreate),
);
?>

<h1>View List <?php echo $model->id; ?>: <?php echo $model->name ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
        'library',
		'values',
		'keywords',
		'created',
		'modified',
        array(
            'name'=>'sql',
            'value'=>nl2br($model->sql),
            'type'=>'raw'
        ),
	),
)); ?>

<center><input type='button' value='Test SQL' id='testSQLbtn' /></center>
<input type='hidden' id='test_sql' value="<?php echo htmlentities($model->sql) ?>" />
<div id='testsql' title='Test SQL'>
    <div id='testsql_results' class='testsql'>
    </div>
</div>
<input type='hidden' name='sqlOK' id='sqlOK' value='0' />