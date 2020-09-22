<?php
/* @var $this RecipientListsController */
/* @var $model RecipientLists */

$this->breadcrumbs=array(
	'Recipient Lists'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List of Lists', 'url'=>array('index')),
	array('label'=>'Create List', 'url'=>array('create')),
	array('label'=>'View Lists', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Lists', 'url'=>array('admin')),
);
?>

<h1>Update Recipient List</h1>

<?php $this->renderPartial('_form', array('model'=>$model, 
                                          'fields'=>$fields, 
                                          'starters'=>$starters,
                                          'library'=>$library)); ?>