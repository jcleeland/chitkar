<?php
/* @var $this FilesController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Files',
);

if(Yii::app()->user->hasFlash('error')) 
{
    echo "<div class='flash-error'>".Yii::app()->user->getFlash('error')."</div>"; 
}

$this->menu=array(
	array('label'=>'Upload a File', 'url'=>array('create')),
	array('label'=>'Manage Uploaded Files', 'url'=>array('admin')),
);
?>

<h1>Files</h1>

<?php
echo CHtml::beginForm(CHtml::normalizeUrl(array('Files/index')), 'get', array('id'=>'filter-form'))
    //. '<label>Filter:</label>'
    . CHtml::textField('string', (isset($_GET['string'])) ? $_GET['string'] : '', array('id'=>'string', 'style'=>'width: 200px'))
    . "&nbsp;"
    //. CHtml::submitButton('Search', array('name'=>''))
    . CHtml::endForm();
?>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
    'enableSorting'=>1,
    'sortableAttributes'=>array(
        'description'=>'File Description',
        'created'=>'Date uploaded',
    )
)); ?>
