<?php
/* @var $this OutgoingsController */
/* @var $model Outgoings */

$this->breadcrumbs=array(
	'Outgoings'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Outgoings', 'url'=>array('index')),
	array('label'=>'Manage Outgoings', 'url'=>array('admin')),
);
?>

<h1>Create Outgoings</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>