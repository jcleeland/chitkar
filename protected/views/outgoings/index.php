<?php
/* @var $this OutgoingsController */
/* @var $dataProvider CActiveDataProvider */
$baseUrl=Yii::app()->baseUrl;
Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/CListFilter.js');

$this->breadcrumbs=array(
	'Outgoings',
);

$this->menu=array(
	array('label'=>'Create Outgoings', 'url'=>array('create')),
	array('label'=>'Manage Outgoings', 'url'=>array('admin')),
);
?>

<h1>Outgoings</h1>
<script type='text/javascript'>
$(document).ready(function() {
    $('#newslettersId').change(function() {
         window.location.href="?r=Outgoings/index&newsletterId="+$('#newslettersId').val();
    });
});
</script>
<?php
echo CHtml::beginForm(CHtml::normalizeUrl(array('Outgoings/index')), 
                      'get', 
                      array('id'=>'filter-form')
                      ) ?>
<p style='margin-bottom: 5px;'>Filter by Recip Id: 

<?php echo CHtml::textField('recipid',(isset($_GET['recipid'])) ? $_GET['recipid'] : '',array('id'=>'recipid','disabled'=>false)) ?>

&nbsp;
Filter by Email: 

<?php echo CHtml::textField('recipemail','',array('id'=>'recipemail', 'disabled'=>false)) ?>

</p>
<!-- FILTERING BY NEWSLETTER - NOT CURRENTLY COMPLETED
<p>Filter by Newsletter: 

<?php echo CHtml::dropDownList('newslettersid','', CHtml::listData(Newsletters::model()->findAll(array("order"=>"title")), 'id', 'title'), array('empty'=>'All', 'style'=>'width: 300px')); ?>
</p>-->

<?php echo CHtml::endForm(); ?>




<br />
<!--<div class='view' style='font-weight: bold; border-bottom: 0'>
    <div class='sqlTableCol2 oddcol headcol' style='width: 10px'>
        &nbsp;
    </div>
    <div class='sqlTableCol2 evencol headcol' style='width: 40px'>
        News ID
    </div>
    <div class='sqlTableCol2 oddcol headcol' style='width: 40px'>
        List
    </div>
    <div class='sqlTableCol2 evencol headcol' style='width: 60px'>
        Recip Id
    </div>
    <div class='sqlTableCol2big oddcol headcol' style='width: 250px'>
        Email
    </div>
    <div class='sqlTableCol2 evencol headcol' style='width: 125px'>
        Queued
    </div>
    <div class='sqlTableCol2 oddcol headcol' style='width: 125px'>
        Sent
    </div>
    <div class='sqlTableCol2 evencol headcol' style='width: 125px'>
        Time Read
    </div>
    <div class='sqlTableCol2 oddcol headcol' style='width: 60px'>
        Link Used
    </div>
</div>-->

<br /><br />     
<?php   
$this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view', 
    'sortableAttributes'=>array(
        't.id',
        'newslettersId',
        'recipientId',
        'email',
        'queueDate',
        'dateSent',
        'readTime',
        'linkUsed',
        ),
    'id'=>'ajaxListView',    
)); 
?>
