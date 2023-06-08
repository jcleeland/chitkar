<?php
/* @var $this OutgoingsController */
/* @var $data Outgoings */
?>

<div class="view" style='height: 40px; margin-bottom: 0; padding: 0'>
	<div>
        <div class='sqlTableCol2big' style='width: 655px; white-space: nowrap; overflow: hidden' title='Newsletter Title'>
        <b><?php echo CHtml::link(CHtml::encode($data->newsletters->title), array('newsletters/view', 'id'=>$data->newslettersId), array('title'=>'View this newsletter')); ?></b>
            [<?php echo CHtml::link("View record", array('view', 'id'=>$data->id), array('title'=>'View this record')); ?>]&nbsp;
        </div>

        <div style='clear: both'></div>
        
        <div class='sqlTableCol2 oddcol' style='width: 10px'>&nbsp;</div>
        
        <!--<div class='sqlTableCol2 evencol' style='width: 40px' title='Newsletter ID'>
	    <?php echo CHtml::link(CHtml::encode($data->newslettersId), array('outgoings/index', 'newslettersid'=>$data->newslettersId), array('title'=>'View outgoings for this newsletter')); ?>
	    </div>

        <div class='sqlTableCol2 oddcol' style='width: 40px' title='Recipient List ID'>
	    <?php echo CHtml::link(CHtml::encode($data->recipientListsId), array('outgoings/index', 'reciplistid'=>$data->recipientListsId), array('title'=>'View all outgoings to this recipient list')); ?>
        </div>-->
        
        <div class='sqlTableCol2 evencol' style='width: 60px' title='Recipient ID'>
	    <?php echo CHtml::link(CHtml::encode($data->recipientId), array('outgoings/index', 'recipid'=>$data->recipientId), array('title'=>'View all reocords for this member')); ?>
        </div>

        <div class='sqlTableCol2big oddcol' style='width: 255px' title='Recipient Email'>
	    <?php echo CHtml::encode($data->email); ?>
        </div>

        <div class='sqlTableCol2 evencol' style='width: 165px' title='Queued date & time'>
	    <b>Q'd:</b> <?php if($data->sendDate != "") echo date("d/m/y H:i", strtotime(CHtml::encode($data->sendDate))); ?>
        </div>

        <div class='sqlTableCol2 oddcol'  style='width: 165px' title='Sent date & time'>
	    <b>Sent:</b> <?php if($data->dateSent != "") echo date("d/m/y H:i", strtotime(CHtml::encode($data->dateSent))); ?>
        </div>

        <div class='sqlTableCol2 evencol'  style='width: 165px' title='Read date & time'>
        <b>Read:</b> <?php if($data->readTime != "0000-00-00 00:00:00" && $data->readTime != "") {echo date("d/m/y H:i", strtotime(CHtml::encode($data->readTime)));} else {echo "No";} ?>
        </div>

        <div class='sqlTableCol2 oddcol' style='width: 60px' title='Link used?'>
        <b>Link:</b>
        <?php
            if($data->linkUsed == 1) {
                echo "Yes";    
            } else {
                echo "No";
            }
        ?>
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