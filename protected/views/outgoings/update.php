<?php
/* @var $this OutgoingsController */
/* @var $model Outgoings */

$this->breadcrumbs=array(
	'Outgoings'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Outgoings', 'url'=>array('index')),
	array('label'=>'Create Outgoings', 'url'=>array('create')),
	array('label'=>'View Outgoings', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Outgoings', 'url'=>array('admin')),
);
?>

<h1>Update Outgoings <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>