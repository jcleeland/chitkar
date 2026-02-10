<?php
/* @var $this UsersController */
/* @var $model Users */

$this->breadcrumbs=array(
	'Users'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List Users', 'url'=>array('index')),
	array('label'=>'Create Users', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#users-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Users</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'users-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'username',    
		'email',
		'firstname',
		'lastname',
		array(
			'name'=>'enabled',
			'header'=>'Enabled',
			'type'=>'raw',
			'filter'=>array(0=>'No',1=>'Yes'),
			'value'=>'CHtml::checkBox("enabled_".$data->id, (bool)$data->enabled, array("class"=>"perm-toggle","data-id"=>$data->id,"data-attr"=>"enabled"))',
		),
		array(
			'name'=>'can_create',
			'header'=>'Create',
			'type'=>'raw',
			'filter'=>array(0=>'No',1=>'Yes'),
			'value'=>'CHtml::checkBox("can_create_".$data->id, (bool)$data->can_create, array("class"=>"perm-toggle","data-id"=>$data->id,"data-attr"=>"can_create"))',
		),
		array(
			'name'=>'can_queue',
			'header'=>'Queue',
			'type'=>'raw',
			'filter'=>array(0=>'No',1=>'Yes'),
			'value'=>'CHtml::checkBox("can_queue_".$data->id, (bool)$data->can_queue, array("class"=>"perm-toggle","data-id"=>$data->id,"data-attr"=>"can_queue"))',
		),
		array(
			'name'=>'can_delete',
			'header'=>'Delete',
			'type'=>'raw',
			'filter'=>array(0=>'No',1=>'Yes'),
			'value'=>'CHtml::checkBox("can_delete_".$data->id, (bool)$data->can_delete, array("class"=>"perm-toggle","data-id"=>$data->id,"data-attr"=>"can_delete"))',
		),
		array(
			'name'=>'can_control',
			'header'=>'Control',
			'type'=>'raw',
			'filter'=>array(0=>'No',1=>'Yes'),
			'value'=>'CHtml::checkBox("can_control_".$data->id, (bool)$data->can_control, array("class"=>"perm-toggle","data-id"=>$data->id,"data-attr"=>"can_control"))',
		),
		array(
			'name'=>'can_admin',
			'header'=>'Admin',
			'type'=>'raw',
			'filter'=>array(0=>'No',1=>'Yes'),
			'value'=>'CHtml::checkBox("can_admin_".$data->id, (bool)$data->can_admin, array("class"=>"perm-toggle","data-id"=>$data->id,"data-attr"=>"can_admin"))',
		),
		/*
		'created',
		'modified',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>

<?php
$toggleUrl = $this->createUrl('togglePermission');
Yii::app()->clientScript->registerScript('user-permission-toggle', "
$(document).on('change', '.perm-toggle', function() {
	var checkbox = $(this);
	var id = checkbox.data('id');
	var attr = checkbox.data('attr');
	var value = checkbox.is(':checked') ? 1 : 0;
	$.ajax({
		type: 'POST',
		url: '$toggleUrl',
		data: {id: id, attr: attr, value: value},
		error: function() {
			alert('Error updating permission.');
			// Revert checkbox state on error
			checkbox.prop('checked', !checkbox.is(':checked'));
		}
	});
});
");
?>
