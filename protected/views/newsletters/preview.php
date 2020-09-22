<div class='emailSurrounds'>
    <div class='emailInner'>
        <div class='emailHead'>
            <div class='emailHeadLeft'><b>Received:</b></div>
            <div class='emailHeadRight'><?php echo date("l, d M Y - g:i a"); ?></div>
            <div style='clear: both'></div>
        </div>
        <div style='padding: 5px'>
            <div class='emailHeadLeft'><b>To:</b></div>
            <div class='emailHeadRight'>Joe Citizen</div>
            <div style='clear: both'></div>
        </div>
        <div style='padding: 5px'>
            <div class='emailHeadLeft'><b>Subject:</b></div>
            <div class='emailHeadRight'><?php echo $subject; ?></div>
            <div style='clear: both'></div>
        </div>
    </div>
    <div class='emailContent'>
    <iframe id='emailContent' src='index.php?r=newsletters/contentpreview&id=<?php echo $id ?>' width='100%' height='450px'></iframe>
    </div>
</div>
<?php
  Yii::app()->end();  
?>
