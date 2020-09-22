<strong>Summary of Links</strong>
<ul>
<?php
    foreach($summary as $row) {
        echo "<li>".$row['link'],": ".$row['total']."</li>\n";
    }
?>
</ul>
<div style='width: 780px'>
    <div class='sqlTableCol1 colHeader' style='width: 255px'>Email</div>
    <div class='sqlTableCol1small colHeader' style='width: 70px'>Recip ID</div>
    <div class='sqlTableCol1 colHeader' style='width: 130px'>Link Used</div>
    <div class='sqlTableCol1 colHeader' style='width: 320px'>Link</div>

    <div style='clear: both'></div>

    <?php foreach ($model as $item) {?>
    <div class='sqlTableCol1' style='width: 255px'><?php echo $item->email ?></div>
    <div class='sqlTableCol1small' style='width: 70px; background-color: white'><?php echo $item->recipientId ?></div>
    <div class='sqlTableCol1' style='width: 130px'><?php if($item->linkUsedTime != "0000-00-00 00:00:00") echo date("g:ia, d M", strtotime($item->linkUsedTime)); ?></div>
    <div class='sqlTableCol1' style='width: 320px; white-space: nowrap'><?php echo $item->link; ?></div>
    <div style='clear: both'></div>
    <?php } ?>
</div>

<?php Yii::app()->end(); ?>
