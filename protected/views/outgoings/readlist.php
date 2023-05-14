<div style='float: right; cursor: pointer; background-color: green; color: white; padding: 1px; border-radius: 3px;' title='Export data as CSV' onClick='downloadInnerHtml("readList.csv", "readListCSV","text/csv");'>CSV</div>
<div class='sqlTableCol1 colHeader' style='width: 255px'>Email</div>
<div class='sqlTableCol1small colHeader' style='width: 70px'>Recip ID</div>
<div class='sqlTableCol1 colHeader' style='width: 150px'>Time Sent</div>
<div class='sqlTableCol1 colHeader' style='width: 120px'>Time Read</div>
<div style='clear: both'></div>
<?php $csvoutput = "Email, Recip Id, Time Sent, Time Read\n"; ?>
<?php foreach ($model as $item) {?>
<div class='sqlTableCol1' style='width: 255px'><?php echo $item->email ?></div>
<div class='sqlTableCol1small' style='width: 70px; background-color: white'><?php echo $item->recipientId ?></div>
<div class='sqlTableCol1' style='width: 150px'><?php echo date("d/m/Y g:ia", strtotime($item->dateSent)); ?></div>
<div class='sqlTableCol1' style='width: 150px'><?php if($item->readTime != "0000-00-00 00:00:00" && $item->readTime != "") echo date("d/m/Y g:ia", strtotime($item->readTime)); ?></div>
<div style='clear: both'></div>
<?php $csvoutput .= $item->email.", ".$item->recipientId.", ".$item->dateSent.", "; ?>
<?php if($item->readTime != "0000-00-00 00:00:00") {
    $csvoutput .= $item->readTime."\n";
} else {
    $csvoutput .= "\n";
}
?>
<?php } ?>
<?php echo "<div style='display: none' id='readListCSV'>".$csvoutput."</div>"; ?>

<?php Yii::app()->end(); ?>