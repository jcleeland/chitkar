<?php
/* @var $this UnsubscribesController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Unsubscribes',
);

$this->menu=array(
	array('label'=>'Create Unsubscribes', 'url'=>array('create')),
	array('label'=>'Manage Unsubscribes', 'url'=>array('admin')),
);
?>

<h1>Unsubscribes</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
