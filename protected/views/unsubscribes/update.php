<?php
/* @var $this UnsubscribesController */
/* @var $model Unsubscribes */

$this->breadcrumbs=array(
	'Unsubscribes'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Unsubscribes', 'url'=>array('index')),
	array('label'=>'Create Unsubscribes', 'url'=>array('create')),
	array('label'=>'View Unsubscribes', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Unsubscribes', 'url'=>array('admin')),
);
?>

<h1>Update Unsubscribes <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>