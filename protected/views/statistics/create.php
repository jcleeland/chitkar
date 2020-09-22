<?php
/* @var $this StatisticsController */
/* @var $model Statistics */

$this->breadcrumbs=array(
	'Statistics'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Statistics', 'url'=>array('index')),
	array('label'=>'Manage Statistics', 'url'=>array('admin')),
);
?>

<h1>Create Statistics</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>