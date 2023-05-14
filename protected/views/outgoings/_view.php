<?php
/* @var $this OutgoingsController */
/* @var $data Outgoings */
?>

<div class="view" style='height: 40px; margin-bottom: 0; padding: 0'>
	<div>
        <div class='sqlTableCol2' style='width: 80px;'>
            [<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id), array('title'=>'View this record')); ?>]&nbsp;
        </div>

        <div class='sqlTableCol2big' style='width: 655px; white-space: nowrap; overflow: hidden' title='Newsletter Title'>
        <b><?php echo CHtml::link(CHtml::encode($data->newsletters->title), array('newsletters/view', 'id'=>$data->newslettersId), array('title'=>'View this newsletter')); ?></b>
        </div>
        <div style='clear: both'></div>
        
        <div class='sqlTableCol2 oddcol' style='width: 10px'>&nbsp;</div>
        
        <div class='sqlTableCol2 evencol' style='width: 40px' title='Newsletter ID'>
	    <?php echo CHtml::link(CHtml::encode($data->newslettersId), array('outgoings/index', 'newslettersid'=>$data->newslettersId), array('title'=>'View outgoings for this newsletter')); ?>
	    </div>

        <div class='sqlTableCol2 oddcol' style='width: 40px' title='Recipient List ID'>
	    <?php echo CHtml::link(CHtml::encode($data->recipientListsId), array('outgoings/index', 'reciplistid'=>$data->recipientListsId), array('title'=>'View all outgoings to this recipient list')); ?>
        </div>
        
        <div class='sqlTableCol2 evencol' style='width: 60px' title='Recipient ID'>
	    <?php echo CHtml::link(CHtml::encode($data->recipientId), array('outgoings/index', 'recipid'=>$data->recipientId), array('title'=>'View all for this recipient id')); ?>
        </div>

        <div class='sqlTableCol2big oddcol' style='width: 250px' title='Recipient Email'>
	    <?php echo CHtml::encode($data->email); ?>
        </div>

        <div class='sqlTableCol2 evencol' style='width: 125px' title='Send Date'>
	    <?php if($data->sendDate != "") echo date("d M Y, H:i", strtotime(CHtml::encode($data->sendDate))); ?>
        </div>

        <div class='sqlTableCol2 oddcol'  style='width: 125px' title='Actually sent'>
	    <?php if($data->dateSent != "") echo date("d M Y, H:i", strtotime(CHtml::encode($data->dateSent))); ?>
        </div>

        <div class='sqlTableCol2 evencol'  style='width: 125px' title='Time read'>
        <?php if($data->readTime != "0000-00-00 00:00:00" && $data->readTime != "") {echo date("d M Y, H:i", strtotime(CHtml::encode($data->readTime)));} else {echo "&nbsp;";} ?>
        </div>

        <div class='sqlTableCol2 oddcol' style='width: 40px' title='Link used?'>
        <?php echo CHtml::encode($data->linkUsed); ?>
        </div>
        <div style='clear: both'></div><br />
    </div>


    <div style='clear: both'></div><br />


  	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('sent')); ?>:</b>
	<?php echo CHtml::encode($data->sent); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('bounce')); ?>:</b>
	<?php echo CHtml::encode($data->bounce); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('bounceText')); ?>:</b>
	<?php echo CHtml::encode($data->bounceText); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('read')); ?>:</b>
	<?php echo CHtml::encode($data->read); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('linkUsed')); ?>:</b>
	<?php echo CHtml::encode($data->linkUsed); ?>
	<br />

	*/ ?>

</div>