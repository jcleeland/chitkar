<?php
/* @var $this RecipientListsController */
/* @var $dataProvider CActiveDataProvider */

$baseUrl=Yii::app()->baseUrl;
Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/CListFilter.js');
Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/externalDb.js');


$this->breadcrumbs=array(
	'Recipient Lists',
);

$this->menu=array(
	array('label'=>'Create List', 'url'=>array('create'), 'visible'=>Yii::app()->user->canCreate),
	array('label'=>'Manage Lists', 'url'=>array('admin'), 'visible'=>Yii::app()->user->canCreate),
);

?>

<h1>Recipient Lists</h1>

<?php

if(Yii::app()->user->isGuest) {
    echo "You are not authorised to view this page";
    die();
}
 
echo CHtml::beginForm(CHtml::normalizeUrl(array('RecipientLists/index')), 'get', array('id'=>'filter-form'))
    //. '<label>Filter:</label>'
    . CHtml::textField('string', (isset($_GET['string'])) ? $_GET['string'] : '', array('id'=>'string', 'style'=>'width: 200px'))
    . CHtml::dropDownList('library', (isset($_GET['library'])) ? $_GET['library'] : 'oms', array('oms'=>'OMS', 'must'=>'MUST', ''=>'All'), array('id'=>'library', 'style'=>'width: 70px; margin-left: 10px'))
    . "&nbsp;"
    //. CHtml::submitButton('Search', array('name'=>''))
    . CHtml::endForm();

    
$this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
    'itemView'=>'_view',
    'sortableAttributes'=>array(
        'name',
        'library',
        'keywords',
        ),
    'id'=>'ajaxListView',
)); 


?>
