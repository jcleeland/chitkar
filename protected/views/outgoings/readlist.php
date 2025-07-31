<div style='float: right; cursor: pointer; background-color: green; color: white; padding: 1px; border-radius: 3px;' title='Export data as CSV' onClick='downloadInnerHtml("readList.csv", "readListCSV","text/csv");'>
    CSV
</div>
<?php $newsletterId=$_GET['id']; ?>
<div class='sqlTableCol1 colHeader updatereadlist' onclick='updatereadlist("<?= $newsletterId ?>", "email");' style='width: 255px; cursor: pointer' title="Sort by Email Address">Email</div>
<div class='sqlTableCol1small colHeader updatereadlist' onclick='updatereadlist("<?= $newsletterId ?>", "recipientId");' style='width: 70px; cursor: pointer' title="Sort by Recipient Id (member no)">Recip ID</div>
<div class='sqlTableCol1 colHeader updatereadlist' onclick='updatereadlist("<?= $newsletterId ?>", "dateSent");' style='width: 150px; cursor: pointer' title="Sort by Time Sent">Time Sent</div>
<div class='sqlTableCol1 colHeader updatereadlist' onclick='updatereadlist("<?= $newsletterId ?>", "readTime");' style='width: 120px; cursor: pointer' title='Sort by Time Read'>Time Read</div>

<div style='clear: both'></div>
<?php
//Sort $model by either email, recipipentId, dateSent or readTime. If the sort is by dateSent or readTime, then sort by the most recent first.
// - always do a secondary sort by email

if(isset($_GET['sort'])) {
    if($_GET['sort'] == "email") {
        usort($model, function($a, $b) {
            return strcmp($a->email, $b->email);
        });
    } elseif($_GET['sort'] == "recipientId") {
        usort($model, function($a, $b) {
            return $a->recipientId - $b->recipientId;
        });
    } elseif($_GET['sort'] == "dateSent") {
        usort($model, function($a, $b) {
            if($a->dateSent == $b->dateSent) {
                return strcmp($a->email, $b->email);
            }
            return strtotime($b->dateSent) - strtotime($a->dateSent);
        });
    } elseif($_GET['sort'] == "readTime") {
        usort($model, function($a, $b) {
            if($a->readTime == $b->readTime) {
                return strcmp($a->email, $b->email);
            }
            return strtotime($b->readTime) - strtotime($a->readTime);
        });
    }
} else {
    usort($model, function($a, $b) {
        return strcmp($a->email, $b->email);
    });
}

?>

<?php $csvoutput = "Email, Recip Id, Time Sent, Time Read\n"; ?>
<?php foreach ($model as $item) {?>
    <div class='sqlTableCol1' style='width: 255px'><?php echo $item->email ?></div>
    <div class='sqlTableCol1small' style='width: 70px; background-color: white'><?php echo $item->recipientId ?></div>
    <div class='sqlTableCol1' style='width: 150px'><?php 
    
    if($item->dateSent) {
        echo date("d/m/Y g:ia", strtotime($item->dateSent));
    } else {
        echo "Not Sent Yet";
    }
    ?></div>
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