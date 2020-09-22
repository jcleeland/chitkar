<?php
/* @var $this UsersController */
/* @var $model Users */
/* @var $form CActiveForm */

$yesNo=array('0'=>'No', '1'=>'Yes');

?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'users-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?> 

	<div class="row">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'password_first'); ?>
		<?php echo $form->passwordField($model,'password_first',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'password_first'); ?>
	</div>

    <div class="row">
        <?php echo $form->labelEx($model,'password_repeat'); ?>
        <?php echo $form->passwordField($model,'password_repeat',array('size'=>60,'maxlength'=>128)); ?>
        <?php echo $form->error($model,'password_repeat'); ?>
    </div>

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'firstname'); ?>
		<?php echo $form->textField($model,'firstname',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'firstname'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'lastname'); ?>
		<?php echo $form->textField($model,'lastname',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'lastname'); ?>
	</div>

    <div class="row" style='float: left; width: 150px'>
        <?php echo $form->labelEx($model,'can_create'); ?>
        <?php echo $form->dropDownList($model, 'can_create', $yesNo) ?>
        <?php echo $form->error($model,'can_create'); ?>
    </div>

    <div class="row" style='float: left; width: 150px'>
        <?php echo $form->labelEx($model,'can_queue'); ?>
        <?php echo $form->dropDownList($model, 'can_queue', $yesNo) ?>
        <?php echo $form->error($model,'can_queue'); ?>
    </div>

    <div class="row" style='float: left; width: 150px'>
        <?php echo $form->labelEx($model,'can_delete'); ?>
        <?php echo $form->dropDownList($model, 'can_delete', $yesNo) ?>
        <?php echo $form->error($model,'can_delete'); ?>
    </div>

    <div class="row" style='float: left; width: 150px'>
        <?php echo $form->labelEx($model,'can_control'); ?>
        <?php echo $form->dropDownList($model, 'can_control', $yesNo) ?>
        <?php echo $form->error($model,'can_control'); ?>
    </div>

    <div class="row" style='float: left; width: 150px'>
        <?php echo $form->labelEx($model,'can_admin'); ?>
        <?php echo $form->dropDownList($model, 'can_admin', $yesNo) ?>
        <?php echo $form->error($model,'can_admin'); ?>
    </div>
    <div style='clear: both'></div>

	<!-- NOT REQUIRED BECAUSE AUTOMATICALLY COMPLETED BY MODEL
    <div class="row">
		<?php echo $form->labelEx($model,'created'); ?>
		<?php echo $form->textField($model,'created'); ?>
		<?php echo $form->error($model,'created'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'modified'); ?>
		<?php echo $form->textField($model,'modified'); ?>
		<?php echo $form->error($model,'modified'); ?>
	</div>
    -->

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->