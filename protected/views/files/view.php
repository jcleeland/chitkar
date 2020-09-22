<?php
/* @var $this FilesController */
/* @var $model Files */

$this->breadcrumbs=array(
	'Files'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Uploaded Files', 'url'=>array('index')),
	array('label'=>'Upload a File', 'url'=>array('create')),
	array('label'=>'Update a File', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Uploaded File', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Uploaded Files', 'url'=>array('admin')),
);
?>

<h1>View Uploaded File #<?php echo $model->id; ?> (<?php echo $model->description ?>)</h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'file_name',
		'file_type',
		'file_size',
        array('name'=>'image',
              'type'=>'raw',
              'value'=>CHtml::image(Yii::app()->dbConfig->getValue('public_web_url')."images/".$model->file_name),
              ),
		'created',
		'modified',
	),
)); ?>
