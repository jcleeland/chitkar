<?php
/* @var $this OutgoingsController */
/* @var $model Outgoings */
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
		<?php echo $form->label($model,'newslettersId'); ?>
		<?php echo $form->textField($model,'newslettersId'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'recipientListsId'); ?>
		<?php echo $form->textField($model,'recipientListsId'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'recipientId'); ?>
		<?php echo $form->textField($model,'recipientId',array('size'=>60,'maxlength'=>256)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>256)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'sendDate'); ?>
		<?php echo $form->textField($model,'sendDate'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'dateSent'); ?>
		<?php echo $form->textField($model,'dateSent'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'sent'); ?>
		<?php echo $form->textField($model,'sent'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'bounce'); ?>
		<?php echo $form->textField($model,'bounce'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'bounceText'); ?>
		<?php echo $form->textArea($model,'bounceText',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'read'); ?>
		<?php echo $form->textField($model,'read'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'linkUsed'); ?>
		<?php echo $form->textField($model,'linkUsed'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->