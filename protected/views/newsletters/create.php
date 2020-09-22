<?php
/* @var $this NewslettersController */
/* @var $model Newsletters */

$this->breadcrumbs=array(
	'Newsletters'=>array('index'),
	'Create',
);

?>
<h1>Create A Newsletter</h1>
<?php

if(Yii::app()->user->can_create) {

$this->menu=array(
    array('label'=>'List Newsletters', 'url'=>array('index')),
    array('label'=>'Manage Newsletters', 'url'=>array('admin')),
);
?>


<?php $this->renderPartial('_form', array('model'=>$model, 'fields'=>$fields, 'starters'=>$starters, 'library'=>$library)); ?>    

<?php
} else {
    echo "You are not authorised to create newsletters";
}
?>
