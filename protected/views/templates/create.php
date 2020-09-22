<?php
/* @var $this TemplatesController */
/* @var $model Templates */

$this->breadcrumbs=array(
	'Templates'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Templates', 'url'=>array('index')),
	array('label'=>'Manage Templates', 'url'=>array('admin')),
);
?>

<h1>Create Templates</h1>

<?php $this->renderPartial('_form', array('model'=>$model, 'types'=>$types)); ?>