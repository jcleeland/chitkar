<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'newsletters-form',
    // Please note: When you enable ajax validation, make sure the corresponding
    // controller action is handling ajax validation correctly.
    // There is a call to performAjaxValidation() commented in generated controller code.
    // See class documentation of CActiveForm for details on this.
    'enableAjaxValidation'=>false,
));
if($newsletter->completed_html=="") {
    $newsletter->completed_html=$content;
}

?>

    <div class="row" style='display: none '>
        <?php echo $form->textArea($newsletter,'completed_html'); ?>
        <?php echo $form->error($newsletter,'completed_html'); ?>
    </div>
    <div class="row" style='display: none'>
        <?php echo $form->textField($newsletter,'queued',array('value'=>1)); ?>
        <?php echo $form->error($newsletter,'queued'); ?>
    </div>


    <div id='page1' class='page'>
        <p class='pageExplain'>
            Please ensure that the newsletter you see below is what you expect. 
            Once you queue the newsletter, changes cannot be made to the email contents. If you are
            not satisifed with the contents of the email, cancel this queue process and edit the newsletter.
        </p>
        <div style='width: 100%; text-align: center'>
            <div class='emailSurrounds' style='text-align: left; width: 780px; margin-left: auto; margin-right: auto'>
                <div class='emailInner'>
                    <div class='emailHead'>
                        <div class='emailHeadLeft'>To:</div>
                        <div class='emailHeadRight'>Joe Citizen</div>
                        <div style='clear: both'></div>
                    </div>
                    <div class='emailHead'>
                        <div class='emailHeadLeft'>Subject:</div>
                        <div class='emailHeadRight'><?php echo $newsletter->subject ?></div>
                        <div style='clear: both'></div>
                    </div>
                </div>
                <div class='emailContent' style='overflow: auto; height: 300px;'>
                    <?php echo $content ?>
                </div>
            </div>
         </div>
         <!-- JASON
         <pre>
         <?php //echo $content; ?>
         </pre>
         -->
         &nbsp;
    </div>
    
    <div id='page2' class='page' style='display: none'>
        <p class='pageExplain'>Please ensure that the time for sending the newsletter listed below is correct.
        Once the newsletter is queued it won't be sent out until this time has been reached.</p>
        <div style='width: 100%; text-align: center'>
            <div class='emailSurrounds' style='width: 780px; margin-left: auto; margin-right: auto'>
                <div class='emailInner'>
                    <div class='emailHead'>
                        <div class='emailHeadLeft'>Send at:</div>
                        <div class='emailHeadRight'><?php echo date("g:i a ", strtotime($newsletter->sendDate)). " on the ".date("jS M, Y", strtotime($newsletter->sendDate)); ?></div>
                        <div style='clear: both'></div>
                    </div>
                </div>
            </div>
            &nbsp;
        </div>        
    </div>    
    <div id='page3' class='page' style='display: none'>
        <?php
            if($newsletter->trackReads == 1) {
                echo "Tracking Reads";
            }
            //print_r($newsletter);
        ?>
        <p class='pageExplain'>Last chance. If you're absolutely sure, click "Queue" now.</p>
    </div>    
<?php $this->endWidget(); ?>

</div><!-- form -->

