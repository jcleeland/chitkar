<?php
/* @var $this NewslettersController */
/* @var $data Newsletters */
$baseUrl=Yii::app()->baseUrl;
Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/preview.js');
Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/externalDb.js');
Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/queue.js');
?>

<div class="view">
    <div class="operations2">
<?php if($data->queued == 0) {
?>
                <a href='' id='<?php echo $data->id ?>' class='previewBtn'>Preview</a> | 
                <a href='' id='<?php echo $data->id; ?>' class='previewSqlBtn'>Recipients</a> 
                <?php if(Yii::app()->user->can_create) echo " | ".CHtml::link('Edit', array('newsletters/update', 'id'=>$data->id)); ?>
<?php    
} else {
?>
                <a href='' id='<?php echo $data->id ?>' class='previewBtn' title='View a preview of this newsletter'>Quick View</a> | 
                <a href='' id='<?php echo $data->id ?>' class='previewStatusBtn' title='Get a quick summary of the status of this newsletter'>Status</a>
<?php
    if($data->archive == 0 && strtotime($data->sendDate) < strtotime("60 days ago") ) {
        ?>
                | <?php echo CHtml::link('Archive', array('archive', 'id'=>$data->id), array('title'=>'Archive this newsletter', 'onClick'=>'return confirm("Archiving saves basic statistics for this newsletter but deletes individual records. It cannot be undone. Are you sure you want to archive this newsletter?")')); ?>
        <?php
    }
}
?>
	</div>
<?php
    if($data->archive == 1) {
        ?>
    <div class="floatLeft">
        <img src='images/archive.png' align='left' width='40px' style='margin-right: 5px' title='This newsletter has been archived' />
    </div>
        
        <?php
        
    }    
?>
    <div class='floatRight'>
        <?php if($data->queued == 0 && Yii::app()->user->can_queue) {
            ?>
                <input type='button' value='Queue for Delivery' id='<?php echo $data->id ?>' class='queueBtn' />
            <?php
        } else {
            ?>
                <div id='status<?php echo $data->id ?>' class='statusFloat'></div>
            <?php
        }
        ?>
    </div>
    <span class="small-title"><?php echo CHtml::link(CHtml::encode($data->title), array('view', 'id'=>$data->id)); ?></span><br />
    <span class='small'><i><?php echo $data->recipientCount ?> recipients |
                           Owned by <?php echo $data->users->firstname; ?> <?php echo $data->users->lastname ?> | 
                           <?php echo $data->templates->name; ?> Template 
                           <?php if($data->recipientListsId && isset($data->recipientLists->name)) {echo " | ".$data->recipientLists->name. " Recipient List";} ?></i></span><br />
    <span class="small"><?php //echo CHtml::encode($data->getAttributeLabel('sendDate')); ?>
	<?php if ($data->queued == 1 && $data->completed == 0) {
    ?>
    Will start sending after     
    <?php
    } elseif ($data->completed == 1) {
    ?>
    Started sending after
    <?php
    }
    ?>
    <?php echo date("h:ia, l d M Y", strtotime(CHtml::encode($data->sendDate))); ?>
    </span>
    <span class='small'><?php if(Yii::app()->user->can_create) echo CHtml::link('[Create new from this..]', array('create', 'copyid'=>$data->id)); ?></span>
    &nbsp;<br />
    <?php /*
    <b><?php echo CHtml::encode($data->getAttributeLabel('completed')); ?>:</b>
    <?php echo Chtml::encode($data->completed) ?>
	<b><?php echo CHtml::encode($data->getAttributeLabel('completed')); ?>:</b>
	<?php echo CHtml::encode($data->completed); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('recipientSql')); ?>:</b>
	<?php echo CHtml::encode($data->recipientSql); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('recipientValues')); ?>:</b>
	<?php echo CHtml::encode($data->recipientValues); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('archive')); ?>:</b>
	<?php echo CHtml::encode($data->archive); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('trackReads')); ?>:</b>
	<?php echo CHtml::encode($data->trackReads); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('trackLinks')); ?>:</b>
	<?php echo CHtml::encode($data->trackLinks); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('trackBounces')); ?>:</b>
	<?php echo CHtml::encode($data->trackBounces); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('recipientCount')); ?>:</b>
	<?php echo CHtml::encode($data->recipientCount); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('created')); ?>:</b>
	<?php echo CHtml::encode($data->created); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('modified')); ?>:</b>
	<?php echo CHtml::encode($data->modified); ?>
	<br />

	*/ ?>

</div>

