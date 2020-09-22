<?php
/* @var $this StatisticsController */
/* @var $model Statistics */

$this->breadcrumbs=array(
	'Statistics'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Statistics', 'url'=>array('index')),
	array('label'=>'Create Statistics', 'url'=>array('create')),
	array('label'=>'View Statistics', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Statistics', 'url'=>array('admin')),
);
?>

<h1>Update Statistics <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>