<?php
/* @var $this TemplatesController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Templates',
);

$this->menu=array(
	array('label'=>'Create Template', 'url'=>array('create'), 'visible'=>Yii::app()->user->canCreate),
	array('label'=>'Manage Templates', 'url'=>array('admin'), 'visible'=>Yii::app()->user->canCreate),
);
?>

<h1>Templates</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
    'sortableAttributes'=>array(
        'name',
        'created',
    ),
)); ?>
