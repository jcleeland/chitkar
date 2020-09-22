<?php
/* @var $this StatisticsController */
/* @var $model Statistics */

$this->breadcrumbs=array(
	'Statistics'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Statistics', 'url'=>array('index')),
	array('label'=>'Create Statistics', 'url'=>array('create')),
	array('label'=>'Update Statistics', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Statistics', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Statistics', 'url'=>array('admin')),
);
?>

<h1>View Statistics #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'date',
		'newslettersQueued',
		'emailsSent',
		'emailsRead',
		'emailBounces',
		'linksUsed',
	),
)); ?>
