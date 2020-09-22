<?php
/* @var $this TemplatesController */
/* @var $model Templates */
/* @var $form CActiveForm */

$baseUrl=Yii::app()->baseUrl;
Yii::app()->clientScript->registerScriptFile($baseUrl.'/ckeditor/ckeditor.js');
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'templates-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
	//added for image upload
    'method'=>'post',
    //'type'=>'horizontal',
    'htmlOptions'=>array('enctype'=>'multipart/form-data')
)); ?>
    
	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'html'); ?>
		<?php echo $form->textArea($model,'html',array('id'=>'html', 'rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'html'); ?>
	</div>
	<!--
    <script type="text/javascript">
    CKEDITOR.replace( 'html' );
	</script>
	-->
    
    <script type="text/javascript">
    CKEDITOR.replace( 'html', {
         filebrowserBrowseUrl: '<?php echo Yii::app()->baseUrl; ?>/kcfinder/browse.php?type=files',
         filebrowserImageBrowseUrl: '<?php echo Yii::app()->baseUrl; ?>/kcfinder/browse.php?type=images',
         filebrowserFlashBrowseUrl: '<?php echo Yii::app()->baseUrl; ?>/kcfinder/browse.php?type=flash',
         filebrowserUploadUrl: '<?php echo Yii::app()->baseUrl; ?>/kcfinder/upload.php?type=files',
         filebrowserImageUploadUrl: '<?php echo Yii::app()->baseUrl; ?>/kcfinder/upload.php?type=images',
         filebrowserFlashUploadUrl: '<?php echo Yii::app()->baseUrl; ?>/kcfinder/upload.php?type=flash'
    });
	</script>
    
	<div class="row">
		<?php echo $form->labelEx($model,'thumb_img'); ?>
		<?php echo $form->fileField($model,'thumb_img'); ?>
		<?php echo $form->error($model,'thumb_img'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->