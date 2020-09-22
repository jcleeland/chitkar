<?php
/* @var $this TemplatesController */
/* @var $model Templates */

$this->breadcrumbs=array(
	'Templates'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Templates', 'url'=>array('index')),
	array('label'=>'Create Template', 'url'=>array('create'), 'visible'=>Yii::app()->user->canCreate),
	array('label'=>'Update Template', 'url'=>array('update', 'id'=>$model->id), 'visible'=>Yii::app()->user->canCreate),
	array('label'=>'Delete Template', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?'), 'visible'=>Yii::app()->user->canDelete),
	array('label'=>'Manage Templates', 'url'=>array('admin'), 'visible'=>Yii::app()->user->canCreate),
);

?>

<h1> <?php echo $model->name; ?> Template</h1>
<div class='emailSurrounds'>
    <div class='emailInner'>
        <div class='emailHead'>
            <div class='emailHeadLeft'><b>Created:</b></div>
            <div class='emailHeadRight'><?php echo $model->created ?></div>
            <div style='clear: both'></div>
        </div>
        <div class='emailHead'>
            <div class='emailHeadLeft'><b>Last edited:</b></div>
            <div class='emailHeadRight'><?php echo $model->modified ?></div>
            <div style='clear: both'></div>
        </div>        <!--<div class='emailHead'>
            <div class='emailHeadLeft'><b>Name:</b></div>
            <div class='emailHeadRight'><img src='<?php echo Yii::app()->createUrl('templates/loadImage', array('id'=>$model->id)); ?>' /></div>
            <div style='clear: both'></div>
        </div>-->
        <div class='emailHead'>
            <div class='emailHeadLeft'><b>Preview:</b></div>
            <div class='emailHeadRight'><iframe id='emailContent' src='?r=templates/contentpreview&id=<?php echo $model->id ?>' width='100%' height='300px'></iframe></div>
            <div style='clear: both'></div>
        </div>
    </div>
</div>

