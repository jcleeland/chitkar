<?php
/* @var $this NewslettersController */
/* @var $model Newsletters */
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
		<?php echo $form->label($model,'usersId'); ?>
		<?php echo $form->textField($model,'usersId'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'recipientListsId'); ?>
		<?php echo $form->textField($model,'recipientListsId'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'templatesId'); ?>
		<?php echo $form->textField($model,'templatesId'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'title'); ?>
		<?php echo $form->textArea($model,'title',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'content'); ?>
		<?php echo $form->textArea($model,'content',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'sendDate'); ?>
		<?php echo $form->textField($model,'sendDate'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'completed'); ?>
		<?php echo $form->textField($model,'completed'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'recipientSql'); ?>
		<?php echo $form->textArea($model,'recipientSql',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'recipientValues'); ?>
		<?php echo $form->textField($model,'recipientValues',array('size'=>60,'maxlength'=>256)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'archive'); ?>
		<?php echo $form->textField($model,'archive'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'trackReads'); ?>
		<?php echo $form->textField($model,'trackReads'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'trackLinks'); ?>
		<?php echo $form->textField($model,'trackLinks'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'trackBounces'); ?>
		<?php echo $form->textField($model,'trackBounces'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'recipientCount'); ?>
		<?php echo $form->textField($model,'recipientCount'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'created'); ?>
		<?php echo $form->textField($model,'created'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'modified'); ?>
		<?php echo $form->textField($model,'modified'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->