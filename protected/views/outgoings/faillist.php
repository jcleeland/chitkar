<div style='width: 780px'>
    <div class='sqlTableCol1small colHeader' style='width: 70px'>Recip ID</div>
    <div class='sqlTableCol1 colHeader' style='width: 255px'>Email</div>
    <div class='sqlTableCol1 colHeader' style='width: 60px'>Failures</div>
    <div class='sqlTableCol1 colHeader' style='width: 320px'>Reason</div>

    <div style='clear: both'></div>

    <?php foreach ($model as $item) {?>
    <div class='sqlTableCol1small' style='width: 70px; background-color: white'><?php echo $item->recipientId ?></div>
    <div class='sqlTableCol1' style='width: 255px'><?php if($item->email) { echo $item->email; } else {echo "&nbsp;"; }?></div>
    <div class='sqlTableCol1' style='width: 60px'><?php echo $item->sendFailures; ?></div>
    <div class='sqlTableCol1' style='width: 320px; white-space: nowrap'><?php echo $item->sendFailureText; ?></div>
    <div style='clear: both'></div>
    <?php } ?>
</div>

<?php Yii::app()->end(); ?>
