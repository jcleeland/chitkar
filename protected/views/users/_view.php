<?php
/* @var $this UsersController */
/* @var $data Users */
?>

<div class="view">

	<span class="small-title"><?php echo CHtml::link(CHtml::encode($data->firstname)." ".($data->lastname), array('view', 'id'=>$data->id)); ?></span>
	<br />
	<span class="small">
	<?php echo CHtml::encode($data->getAttributeLabel('username')); ?>:
	<?php echo CHtml::encode($data->username); ?>
	<br />

	<?php echo CHtml::encode($data->getAttributeLabel('email')); ?>:
	<?php echo CHtml::encode($data->email); ?>
	<br />
    <?php if($data->can_create) echo CHtml::encode($data->getAttributeLabel('can_create')); ?>
    <?php if($data->can_queue) echo CHtml::encode($data->getAttributeLabel('can_queue')); ?>
    <?php if($data->can_delete) echo CHtml::encode($data->getAttributeLabel('can_delete')); ?>
    <?php if($data->can_control) echo CHtml::encode($data->getAttributeLabel('can_control')); ?>
    <?php if($data->can_admin) echo CHtml::encode($data->getAttributeLabel('can_admin')); ?>
    <br />
    
	<?php echo CHtml::encode($data->getAttributeLabel('created')); ?>:
	<?php echo CHtml::encode($data->created); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('modified')); ?>:</b>
	<?php echo CHtml::encode($data->modified); ?>
	<br />

	*/ ?>
	</span>
</div>