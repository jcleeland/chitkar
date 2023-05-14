<?php
/* @var $this FilesController */
/* @var $model Files */

$this->breadcrumbs=array(
	'Files'=>array('index')
);

$this->menu=array(
);
?>

<h1>Unused Files</h1> 
<p>These files are not currently being used by an active newsletter and can be deleted if you don't need them.</p>
<?php 
//echo "<pre>"; print_r($dataProvider); echo "</pre>";
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'file-grid',
    'dataProvider'=>$dataProvider,
    'columns'=>array(
        array(
            'name' => 'id',
            'header' => 'ID',
            'type' => 'raw',
            'value' => 'CHtml::link($data["id"], array("files/view", "id"=>$data["id"]))',
        ),
        'file_name',
        'description', 
        'created',
        // ... add more columns here
    ),
));
?>
