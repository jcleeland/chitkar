<?php
/* @var $this FilesController */
/* @var $data Files */
$imageurl = Yii::app()->dbConfig->getValue('public_web_url') . "images/";
?>

<div class="view-column">
    <!-- Column Content Start -->
    <b><?php echo CHtml::link(CHtml::encode($data->description), array('view', 'id' => $data->id)); ?></b>
    <br />
    <b>File Details:</b>
    <?php echo CHtml::encode($data->file_name); ?>
    (<?php echo CHtml::encode($data->file_type); ?>)
    <i><?php echo CHtml::encode($data->file_size); ?>kb </i>
    <br />

    <b><?php echo CHtml::encode($data->getAttributeLabel('created')); ?>:</b>
    <?php echo CHtml::encode($data->created); ?>
    <br />
    <b><?php echo CHtml::encode($data->getAttributeLabel('modified')); ?>:</b>
    <?php echo CHtml::encode($data->modified); ?>
    <br />
    <?php $thisimage = "<img src='$imageurl" . $data->file_name . "' style='max-width: 100%' />"; ?>
    <?php echo CHtml::link($thisimage, array('view', 'id' => $data->id)); ?>
    <!-- Column Content End -->
</div>
