<?php
/* @var $this StatisticsController */
/* @var $model Statistics */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id'); ?>
		<?php echo $form->textField($model,'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'date'); ?>
		<?php echo $form->textField($model,'date'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'newslettersQueued'); ?>
		<?php echo $form->textField($model,'newslettersQueued'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'emailsSent'); ?>
		<?php echo $form->textField($model,'emailsSent'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'emailsRead'); ?>
		<?php echo $form->textField($model,'emailsRead'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'emailBounces'); ?>
		<?php echo $form->textField($model,'emailBounces'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'linksUsed'); ?>
		<?php echo $form->textField($model,'linksUsed'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->