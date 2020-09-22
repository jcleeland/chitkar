<?php
/* @var $this OutgoingsController */
/* @var $model Outgoings */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'outgoings-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'newslettersId'); ?>
		<?php echo $form->textField($model,'newslettersId'); ?>
		<?php echo $form->error($model,'newslettersId'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'recipientListsId'); ?>
		<?php echo $form->textField($model,'recipientListsId'); ?>
		<?php echo $form->error($model,'recipientListsId'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'recipientId'); ?>
		<?php echo $form->textField($model,'recipientId',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'recipientId'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'sendDate'); ?>
		<?php echo $form->textField($model,'sendDate'); ?>
		<?php echo $form->error($model,'sendDate'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'dateSent'); ?>
		<?php echo $form->textField($model,'dateSent'); ?>
		<?php echo $form->error($model,'dateSent'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'sent'); ?>
		<?php echo $form->textField($model,'sent'); ?>
		<?php echo $form->error($model,'sent'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'bounce'); ?>
		<?php echo $form->textField($model,'bounce'); ?>
		<?php echo $form->error($model,'bounce'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'bounceText'); ?>
		<?php echo $form->textArea($model,'bounceText',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'bounceText'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'read'); ?>
		<?php echo $form->textField($model,'read'); ?>
		<?php echo $form->error($model,'read'); ?>
	</div>

    <div class="row">
        <?php echo $form->labelEx($model,'readTime'); ?>
        <?php echo $form->textField($model,'readTime'); ?>
        <?php echo $form->error($model,'readTime'); ?>
    </div>
   	<div class="row">
		<?php echo $form->labelEx($model,'linkUsed'); ?>
		<?php echo $form->textField($model,'linkUsed'); ?>
		<?php echo $form->error($model,'linkUsed'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->