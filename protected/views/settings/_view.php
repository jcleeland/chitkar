<?php
/* @var $this SettingsController */
/* @var $data Settings */
?>

<div class="view">

	<h2><?php echo CHtml::link(CHtml::encode($data->setting_name), array('view', 'id'=>$data->id)); ?></h2>
    <b><?php echo CHtml::encode($data->getAttributeLabel('setting_group')); ?>:</b>
    <?php echo CHtml::encode($data->setting_group); ?><br />
	<b><?php echo CHtml::encode($data->getAttributeLabel('setting_value')); ?>:</b>
	<?php echo CHtml::encode($data->setting_value); ?>
	<br />
	<b><?php echo CHtml::encode($data->getAttributeLabel('setting_description')); ?>:</b>
	<?php echo CHtml::encode($data->setting_description); ?>



</div>