<?php
/* @var $this StatisticsController */
/* @var $data Statistics */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('date')); ?>:</b>
	<?php echo CHtml::encode($data->date); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('newslettersQueued')); ?>:</b>
	<?php echo CHtml::encode($data->newslettersQueued); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('emailsSent')); ?>:</b>
	<?php echo CHtml::encode($data->emailsSent); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('emailsRead')); ?>:</b>
	<?php echo CHtml::encode($data->emailsRead); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('emailBounces')); ?>:</b>
	<?php echo CHtml::encode($data->emailBounces); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('linksUsed')); ?>:</b>
	<?php echo CHtml::encode($data->linksUsed); ?>
	<br />


</div>