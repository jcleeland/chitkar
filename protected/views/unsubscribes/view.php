<?php
/* @var $this UnsubscribesController */
/* @var $model Unsubscribes */

$this->breadcrumbs=array(
	'Unsubscribes'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Unsubscribes', 'url'=>array('index')),
	array('label'=>'Create Unsubscribes', 'url'=>array('create')),
	array('label'=>'Update Unsubscribes', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Unsubscribes', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Unsubscribes', 'url'=>array('admin')),
);
?>

<h1>View Unsubscribes #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'email',
		'recipientId',
		'created',
		'values',
	),
)); ?>
