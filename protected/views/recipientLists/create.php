<?php
/* @var $this RecipientListsController */
/* @var $model RecipientLists */
//Load the specific javascript for this library

$this->breadcrumbs=array(
	'Recipient Lists'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List of Lists', 'url'=>array('index')),
	array('label'=>'Manage Lists', 'url'=>array('admin')),
);
?>

<h1>Create Recipient List</h1>

<?php $this->renderPartial('_form', array('model'=>$model, 
                                          'fields'=>$fields, 
                                          'starters'=>$starters,
                                          'library'=>$library)); ?>