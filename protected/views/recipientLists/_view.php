<?php
/* @var $this RecipientListsController */
/* @var $data RecipientLists */
?>

<div class="view">

	<!--<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />-->
    
    <!--<b><?php echo CHtml::encode($data->getAttributeLabel('sql')); ?>:</b>
	<?php echo CHtml::encode($data->sql); ?>
	<br />-->
    
	<span class="small-title"><?php echo CHtml::link(CHtml::encode($data->name), array('view', 'id'=>$data->id)); ?></span>: 
    <span class="small"><?php echo CHtml::encode($data->getAttributeLabel('values')); ?>: <?php echo CHtml::encode($data->values); ?>, 
		<?php echo CHtml::encode($data->getAttributeLabel('keywords')); ?>: <?php echo CHtml::encode($data->keywords); ?> <br />
		<?php echo CHtml::encode($data->getAttributeLabel('library')); ?>: <?php echo CHtml::encode($data->library); ?> <br />
        <?php echo CHtml::encode($data->getAttributeLabel('created')); ?>: <?php echo CHtml::encode($data->created); ?>, 
		<?php echo CHtml::encode($data->getAttributeLabel('modified')); ?>: <?php echo CHtml::encode($data->modified); ?>
	</span>

</div>