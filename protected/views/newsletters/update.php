<?php
/* @var $this NewslettersController */
/* @var $model Newsletters */

$this->breadcrumbs=array(
	'Newsletters'=>array('index'),
	$model->title=>array('view','id'=>$model->id),
	'Update',
);
?>
<h1>Edit Newsletter #<?php echo $model->id; ?></h1>
<?php

if(Yii::app()->user->can_create) {
    $this->menu=array(
        array('label'=>'List Newsletters', 'url'=>array('index')),
        array('label'=>'Create a Newsletter', 'url'=>array('create')),
        array('label'=>'View Newsletters', 'url'=>array('view', 'id'=>$model->id)),
        array('label'=>'Manage Newsletters', 'url'=>array('admin')),
    );
    $this->renderPartial('_form', array('model'=>$model, 'fields'=>$fields, 'starters'=>$starters, 'library'=>$library)); 
} else {
    echo "You are not authorised to create or edit newsletters";
}    

?>