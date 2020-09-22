<?php
/* @var $this TemplatesController */
/* @var $data Templates */
?>

<div class="view">
	<?php
	/*
	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	*/
	?>
	<span class="small-title"><?php echo CHtml::link(CHtml::encode($data->name), array('view', 'id'=>$data->id)); ?></span> (Id #<?php echo $data->id ?>)<br />
	<?php
	/*
	<b><?php echo CHtml::encode($data->getAttributeLabel('header_html')); ?>:</b>
	<?php echo CHtml::encode($data->header_html); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('footer_html')); ?>:</b>
	<?php echo CHtml::encode($data->footer_html); ?>
	<br />
	
	<b><?php echo CHtml::encode($data->getAttributeLabel('thumb_img')); ?>:</b>
	*/ ?>
	<?php 
    echo CHtml::link(CHtml::image(Yii::app()->createUrl('templates/loadImage', array('id'=>$data->id))), array('view', 'id'=>$data->id));
	if ((Yii::app()->dbConfig->getValue('default_template')) && (Yii::app()->dbConfig->getValue('default_template') == $data->id)) {
        echo "<br /><span style='font-size: 8pt; font-style: italic'>** System default template</span>";
    }
    ?>
	<br />

</div>
