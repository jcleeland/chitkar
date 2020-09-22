<?php
/* @var $this NewslettersController */
/* @var $model Newsletters */

$this->breadcrumbs=array(
	'Newsletters'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List Newsletters', 'url'=>array('index')),
	array('label'=>'Create a Newsletter', 'url'=>array('create'), 'visible'=>Yii::app()->user->canCreate),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#newsletters-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Newsletters</h1>

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
	'id'=>'newsletters-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
//		'id',
//		'usersId',
//        'users.fullname', 
        array('class'=>'CDataColumn',
              'name'=>'Owner',
              'value'=>'$data->users->fullname',
              'sortable'=>true,
              'filter'=>true),
//		'recipientListsId',
//        'recipientLists.name',
        array('class'=>'CDataColumn',
              'name'=>'List',
              'value'=>'$data->recipientLists->name',
              'sortable'=>true,
              'filter'=>true),
//		'templatesId',
        array('class'=>'CDataColumn',
              'name'=>'Template',
              'value'=>'$data->templates->name',
              'sortable'=>true,
              'filter'=>true),
		'title',
		'sendDate',
//		'completed',
//		'recipientSql',
//		'recipientValues',
//		'archive',
//		'trackReads',
//		'trackLinks',
//		'trackBounces',
//		'recipientCount',
		'created',
		'modified',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
