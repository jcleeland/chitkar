<?php
/* @var $this FilesController */
/* @var $model Files */

$this->breadcrumbs=array(
	'Files'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Uploaded Files', 'url'=>array('index')),
	array('label'=>'Upload a File', 'url'=>array('create')),
	array('label'=>'View Uploaded Files', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Uploaded Files', 'url'=>array('admin')),
);
?>

<h1>Update Files <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>