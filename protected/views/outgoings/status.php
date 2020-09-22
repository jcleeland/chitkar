<div class='speech-border right'>
    <?php echo $statistics['sent']." of ".$statistics['total']." emails sent (".$statistics['percentsent']."%)<br />";?>
    <div class='sqlTableCol1small'>
        Queued:
    </div>
    <div class='sqlTableCol2big'>
        <?php echo $statistics['queued'] ?>
    </div>
    <div style='clear: both'></div>
    <div class='sqlTableCol1small'>
        Set to Start:
    </div>
    <div class='sqlTableCol2big'>
        <?php echo $statistics['started']; ?>
    </div>
    <div style='clear: both'></div>
    <div class='sqlTableCol1small'>
        Last Sent:
    </div>
    <div class='sqlTableCol2big'>
        <?php echo $statistics['lastsent']; ?>
    </div>
    <div style='clear: both'></div>
   
   <?php
    if($newsletter->trackReads == 1) {
    ?>
    <div class='sqlTableCol1small'>
        Reads:
    </div>
    <div class='sqlTableCol2big'>
        <?php echo $statistics['read']." (".$statistics['percentread']."%)"; ?>
    </div>
    <div style='clear: both'></div>
    <?php    
    }
    if($newsletter->trackLinks == 1) {
    ?>
    <div class='sqlTableCol1small'>
        Links:
    </div>
    <div class='sqlTableCol2big'>
        <?php echo $statistics['links']." (".$statistics['percentlinked']."%)"; ?>
    </div>
    <div style='clear: both'></div>
    <?php
    }
    ?>
</div>

<?php Yii::app()->end(); ?>