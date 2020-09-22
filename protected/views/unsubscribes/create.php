<?php
/* @var $this UnsubscribesController */
/* @var $model Unsubscribes */

$this->breadcrumbs=array(
	'Unsubscribes'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Unsubscribes', 'url'=>array('index')),
	array('label'=>'Manage Unsubscribes', 'url'=>array('admin')),
);
?>

<h1>Create Unsubscribes</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>