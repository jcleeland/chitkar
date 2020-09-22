<?php
/* @var $this SettingsController */
/* @var $model Settings */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'settings-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'setting_name'); ?>
		<?php echo $form->textField($model,'setting_name',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'setting_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'setting_value'); ?>
		<?php echo $form->textField($model,'setting_value',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'setting_value'); ?>
	</div>

    <div class="row">
        <?php echo $form->labelEx($model,'setting_group'); ?>
        <?php echo $form->dropDownList($model, 'setting_group', array("General"=>"General", "Email"=>"Email", "FTP"=>"FTP", "External DB"=>"External DB"), array('prompt'=>'Please choose...')); ?>
                        <?php echo $form->error($model,'setting_value'); ?>
    </div>

	<div class="row">
		<?php echo $form->labelEx($model,'setting_description'); ?>
		<?php echo $form->textArea($model,'setting_description',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'setting_description'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->