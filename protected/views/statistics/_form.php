<?php
/* @var $this StatisticsController */
/* @var $model Statistics */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'statistics-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'date'); ?>
		<?php echo $form->textField($model,'date'); ?>
		<?php echo $form->error($model,'date'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'newslettersQueued'); ?>
		<?php echo $form->textField($model,'newslettersQueued'); ?>
		<?php echo $form->error($model,'newslettersQueued'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'emailsSent'); ?>
		<?php echo $form->textField($model,'emailsSent'); ?>
		<?php echo $form->error($model,'emailsSent'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'emailsRead'); ?>
		<?php echo $form->textField($model,'emailsRead'); ?>
		<?php echo $form->error($model,'emailsRead'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'emailBounces'); ?>
		<?php echo $form->textField($model,'emailBounces'); ?>
		<?php echo $form->error($model,'emailBounces'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'linksUsed'); ?>
		<?php echo $form->textField($model,'linksUsed'); ?>
		<?php echo $form->error($model,'linksUsed'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->