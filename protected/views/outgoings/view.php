<?php
/* @var $this OutgoingsController */
/* @var $model Outgoings */

$this->breadcrumbs=array(
	'Outgoings'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Outgoings', 'url'=>array('index')),
	array('label'=>'Create Outgoings', 'url'=>array('create')),
	array('label'=>'Update Outgoings', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Outgoings', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Outgoings', 'url'=>array('admin')),
);
?>

<h1>View Outgoings #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'newslettersId',
		'recipientListsId',
		'recipientId',
		'email',
		'sendDate',
		'dateSent',
		'sent',
		'bounce',
		'bounceText',
		'read',
		'linkUsed',
        'data',
	),
)); ?>
