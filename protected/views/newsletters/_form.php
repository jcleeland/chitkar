<?php
/* @var $this NewslettersController */
/* @var $model Newsletters */
/* @var $form CActiveForm */




$baseUrl=Yii::app()->baseUrl;
Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/Newsletters.js');
Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/externalDb.js');
Yii::app()->clientScript->registerScriptFile($baseUrl.'/ckeditor/ckeditor.js');
Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/formPageProtect.js');
Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/chosen/chosen.jquery.js');
Yii::app()->clientScript->registerCssFile($baseUrl.'/js/chosen/chosen.css');
$yesNo=array('0'=>'No', '1'=>'Yes');


$icsContent = $model->icsContent ?? '';

$dummyModel = new DummyModel(); //This is a dummy model to store the ICS file data if required

// Check if icsContent is not empty and then parse it
if (!empty($icsContent)) {
    //echo "There is icsContent";
    //echo "<pre>".$icsContent."</pre>";
    $unfoldedContent = preg_replace("/\r\n[ \t]|[\n\r][ \t]/", " ", $icsContent);
    //echo "<hr /><pre>".$unfoldedContent."</pre>";
    $lineEnding = strpos($unfoldedContent, "\r\n") === false ? "\n" : "\r\n";
    $lines = explode($lineEnding, $unfoldedContent);
    foreach ($lines as $line) {
        if (strpos($line, 'SUMMARY:') === 0) {
            $dummyModel->eventTitle = substr($line, 8);
        } elseif (strpos($line, 'DSTAMP:') === 0) {
            $dummyModel->dtStamp = substr($line, 7);
        } elseif (strpos($line, 'DTSTART;TZID=Australia/Melbourne:') === 0) {
            $dummyModel->eventStart = convertICalDateToDateTime(substr($line, 33));
        } elseif (strpos($line, 'UID:') === 0) {
            $dummyModel->uid = substr($line, 4);
        } elseif (strpos($line, 'ORGANIZER;CN=') === 0) {
            // Extract the organizer's name and email
            $organizerInfo = str_replace('ORGANIZER;CN=', '', $line);
            $parts = explode(':mailto:', $organizerInfo);
            if (count($parts) == 2) {
                $dummyModel->eventOrganiserName = $parts[0];
                $dummyModel->eventOrganiserEmail = $parts[1];
            }
        } elseif (strpos($line, 'DTEND;TZID=Australia/Melbourne:') === 0) {
            $dummyModel->eventEnd = convertICalDateToDateTime(substr($line, 31));
        } elseif (strpos($line, 'LOCATION:') === 0) {
            $dummyModel->eventLocation = substr($line, 9);
        } elseif (strpos($line, 'DESCRIPTION:') === 0) {
            //$dummyModel->eventDescription = substr($line, 12);
            $dummyModel->eventDescription = str_replace("\\n", "\n", substr($line, 12));
        }
    }
}

// Add one day to the current time
$nextDay = strtotime("+1 day", time());

// Check if the next day is a weekend (Saturday or Sunday)
// and if so, add additional days to make it a weekday
while (date('N', $nextDay) >= 6) { // 6 = Saturday, 7 = Sunday
    $nextDay = strtotime("+1 day", $nextDay);
}

?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'newsletters-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>
    <?php echo $form->hiddenField($model, 'icsContent', array('id'=>'modelIcsContent')); ?>
    <?php echo $form->hiddenField($dummyModel, 'uid', array('id'=>'DummyModel_uid')); ?>

    <?php
        // Assuming $localDateTime is your local date and time in 'Y-m-d H:i:s' format
        $localDateTime = '2024-01-23 20:00:00'; // For example, 20:00 in Melbourne time
        // Create a DateTime object with the local time zone
        $dateTime = new DateTime($localDateTime, new DateTimeZone('Australia/Melbourne'));
        // Convert the time to UTC
        $dateTime->setTimezone(new DateTimeZone('UTC'));
        // Format the date and time for DTSTAMP in UTC
        $dtstamp = $dateTime->format('Ymd\THis\Z');
        if(!$dummyModel->dtStamp) {
            $dummyModel->dtStamp=$dtstamp;
        }
        echo $form->hiddenField($dummyModel, 'dtStamp', array('id'=>'DummyModel_dtStamp'));
    ?>

    <?php //echo CHtml::activeHiddenField($model, 'icsContent', array('id' => 'modelIcsContent')); ?>
    
    <div id='page1' class='page'>
        <p class='pageTitle'>Give your newsletter a reference name.</p>
        <p class='pageExplain'>This will not appear in the newsletter, but is used as a reference name for this newsletter.</p>
        <input type='button' value='<< Prev' disabled='true'> <input type='button' value='Next >>' class='nextBtn'>
        
        <div class='pageContent'>
            <div class="row">
                <?php echo $form->labelEx($model,'title'); ?>
                <?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>255)); ?>
                <?php echo $form->error($model,'title'); ?>
            </div>
            
        </div>
    </div>
    
    <div id='page2' class='page'>
        <p class='pageTitle'>Who is creating this newsletter?</p>
        <p class='pageExplain'>Usually leave this as yourself.</p>
        <input type='button' value='<< Prev' class='prevBtn'>
        <input type='button' value='Next >>' class='nextBtn'>
        <div class='pageContent'>
	        <div class="row">
                <?php $currentuser=Yii::app()->user->id; ?>
                <?php $userlist=CHtml::listData(Users::model()->findall(array('order'=>'lastname')), 'id', 'fullName'); ?>
		        <?php echo $form->labelEx($model,'Owner'); ?>
                <?php echo $form->dropDownList($model, 'usersId', $userlist, array('options'=>array($currentuser=>array('selected'=>true)))); ?>
		        <?php echo $form->error($model,'usersId'); ?>
	        </div>
        </div>
    </div>

    <div id='page3' class='page'>
        <p class='pageTitle'>Select a recipient list, or create one</p>
        <p class='pageExplain'>Use the dropdown menu to choose from pre-defined lists.</p>
        <input type='button' value='<< Prev' class='prevBtn'>
        <input type='button' value='Next >>' class='nextBtn'>
        <div class='pageContent'>
	        <div class="row">
                <?php $recipientlist=CHtml::listData(RecipientLists::model()->findall(array("condition"=>"library='$library'", 'order'=>'name')), 'id', 'name'); ?>
                <?php echo $form->labelEx($model,'recipientListsId'); ?>
		        <?php echo $form->dropDownList($model, 'recipientListsId', 
                                                $recipientlist, 
                                                array('prompt'=>'Create my own',
                                                      'class'=>'chosen-select',) 
                                                ); ?>
                <?php echo $form->error($model,'recipientListsId'); ?>
	        </div>
            <input type='button' id='buildSQLbtn' value='Build SQL' />
            <div id='recipientListInfo'>
                <div class="row">
                    <?php echo $form->labelEx($model,'recipientSql'); ?>
                    <?php echo $form->textArea($model,'recipientSql',array('rows'=>10, 'cols'=>80)); ?>
                    <?php echo $form->error($model,'recipientSql'); ?>
                    <input type='button' style='font-size: 0.7em; padding: 1px !important;' id='copySQLbtn' value='Use as new' />

                </div>
                <div class="row">
                    <?php echo $form->labelEx($model,'recipientValues'); ?>
                    <?php echo $form->textField($model,'recipientValues',array('size'=>60,'maxlength'=>256)); ?>
                    <?php echo $form->error($model,'recipientValues'); ?>
                </div>
            </div>
            <input type='hidden' id='test_sql' />
            <input type='button' value='Test SQL' id='testSQLbtn' /><br /><br />
        </div>
    </div>

    <div id='page4' class='page'>
        <p class='pageTitle'>Choose a template and create your newsletter</p>
        <p class='pageExplain floatRight border' style='border: 1px solid #ccc; max-width: 280px' id='replacementFields'></p>
        <p class='pageExplain'>Enter the subject line, and then use the content field to create the actual contents of your email.</p> 
        <input type='button' value='<< Prev' class='prevBtn'>
        <input type='button' value='Next >>' class='nextBtn'>
        <div class='pageContent'>
	        <div class="row">
                <?php $templatelist=CHtml::listData(Templates::model()->findall(array('order'=>'name')), 'id', 'name'); ?>
		        <?php echo $form->labelEx($model,'templatesId'); ?>
		        <?php 
                    $templateoptions=array();
                
                    if(Yii::app()->dbConfig->getValue('default_template')) {$templateoptions=array(Yii::app()->dbConfig->getValue('default_template')=>array('selected'=>true));} 
                ?>
                <?php echo $form->dropDownList($model, 'templatesId', $templatelist, array('prompt'=>'Please choose...', 'options'=>$templateoptions)); ?>
                <?php echo $form->error($model,'templatesId'); ?>
	        </div>

            <div class="row">
                <?php echo $form->labelEx($model, 'subject'); ?>
                <?php echo $form->textField($model, 'subject', array('size'=>60,'maxlength'=>255)); ?>
                <?php echo $form->error($model, 'subject'); ?>
            </div>
            
	        <div class="row">
		        <?php echo $form->labelEx($model,'content'); ?>
		        <?php echo $form->textArea($model,'content',array('rows'=>6, 'cols'=>90)); ?>
		        <?php echo $form->error($model,'content'); ?>
	        </div>
            <script type="text/javascript">
            CKEDITOR.replace( 'Newsletters_content', {
                "imageBrowser_listUrl": "<?php echo $baseUrl ?>/?r=files/imagelist_json"
            } );
            </script>
        </div>
    </div>

    <div id='page5' class='page'>
        <p class='pageTitle'>Add a Calendar Event (.ics File)</p>
        <p class=''>Note: This function adds an "ics" attachment to the newsletter that allows recipients to automatically add the meeting into their digital calendar. You still need to have a newsletter, and you'll need to enter the details for the meeting below.</p>
        <input type='button' value='<< Prev' class='prevBtn'>
        <input type='button' value='Next >>' class='nextBtn'>
        <div class='pageContent'>
            <div class="row">
                <?php echo CHtml::label('Add a Calendar Event attachment?', 'addCalendarEvent', array('style'=>'font-size: 1.2em')); ?>
                <?php
                $initialCalendarEventValue = !empty($model->icsContent) ? 'yes' : 'no';
                ?>

                <?php echo CHtml::dropDownList(
                    'addCalendarEvent', 
                    $initialCalendarEventValue, 
                    array('no' => 'No', 'yes' => 'Yes'), 
                    array(
                        'id' => 'addCalendarEvent',
                        'style' => 'font-size: 1.2em; min-width: 75px'
                    ),
                    array()
                ); ?>

            </div>
            <div id="calendarEventFields" style="display: none; ">
                <div style="flex: 1; padding-right: 20px;">
                    <div class="row">
                        <?php echo CHtml::label('Calander Invitation Type', 'eventAttachmentType'); ?>
                        <?php echo CHtml::dropDownList(
                            'eventAttachmentType',
                            $dummyModel->eventAttachmentType,
                            array('all'=>'All email clients (.ics file)', 'msoutlook'=>'Microsoft Outlook Only Clients (accept/reject)'),
                            array('id'=>'DummyModel_eventAttachmentType'),
                        );
                        ?>
                    </div>
                    <div class="row">
                        <?php echo CHtml::label('Event Title', 'eventTitle'); ?>
                        <?php echo CHtml::activeTextField($dummyModel, 'eventTitle', array('size'=>'60')); ?>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <div class="row" style="flex: 1; padding-right: 10px;">
                        <input type='hidden' name='defaultstartdate' id='defaultstartdate' value='<?php echo date('Y-m-d H:00:00', $nextDay) ?>' />
                            <?php if(empty($dummyModel->eventStart)) {
                                $dummyModel->eventStart=date('Y-m-d H:00:00', $nextDay);
                            } ?>                    
                            <?php echo CHtml::label('Start Date and Time', 'eventStart'); ?>
                            <?php $this->widget('application.extensions.timepicker.timepicker', array(
                                'model'=>$dummyModel,
                                'name'=>'eventStart',
                                'id'=>'DummyModel_eventStart',
                                'options'=>array('defaultValue'=> 'hi')
                                ));
                            ?>
                        </div>
                        <div class="row" style="flex: 1; padding-left: 10px;">
                            <input type='hidden' name='defaultenddate' id='defaultenddate' value='<?php echo date('Y-m-d H:00:00', $nextDay+3600) ?>' />
                            <?php if(empty($dummyModel->eventEnd)) {
                                $dummyModel->eventEnd=date('Y-m-d H:00:00', $nextDay+3600);
                            } ?>                       
                            <?php echo CHtml::label('End Date and Time', 'eventEnd'); ?>
                            <?php $this->widget('application.extensions.timepicker.timepicker', array(
                                'model'=>$dummyModel,
                                'name'=>'eventEnd',
                                'id' =>'DummyModel_eventEnd',
                                'options'=>array('defaultValue'=> 'hi'),
                                ));
                            ?>
                        </div>
                    </div>
                    <div class="row" style="position: relative">
                        <div id="datewarning" style="border: 1px solid black; position: absolute; background-color: orange; text-align: center; font-weight: bold; font-size: 16px; display: none; border-radius: 20px; color: white; padding: 10px; top: -20px">
                            End date & time has been updated so that it it isn't BEFORE the start date and time.
                        </div>
                    </div>
                    <div class="row">
                        <?php echo CHtml::label('Event Organiser Name', 'eventOrganiserName'); ?>
                        Replace this default value with the name of the Organiser
                        <?php echo CHtml::activeTextField($dummyModel, 'eventOrganiserName', array('size'=>'60')); ?>
                    </div>
                    <div class="row">
                        <?php echo CHtml::label('Event Organiser Email', 'eventOrganiserEmail'); ?>
                        Replace this default value with the email of the Organiser
                        <?php echo CHtml::activeTextField($dummyModel, 'eventOrganiserEmail', array('size'=>'60')); ?>
                    </div> 
                    <div class="row">
                        <?php echo CHtml::label('Event Location', 'eventLocation'); ?>
                        Write the physical (or virtual) location of the meeting. For an online meeting, paste the URL here.
                        <?php echo CHtml::activeTextField($dummyModel, 'eventLocation', array('size'=>'60')); ?>
                    </div>
                    <div class="row">
                        <?php echo CHtml::label('Event Description', 'eventDescription'); ?>
                        Give a brief description about the meeting. You can also include a simple agenda here. Note that formatting is not available, this is text only.
                        <?php echo CHtml::activeTextArea($dummyModel, 'eventDescription', array('rows'=>'6', 'cols'=>'80')); ?>
                    </div>
                </div>
                <div style="flex: 1; max-width: 400px; padding-left: 20px; overflow-x: auto; max-height: 400px; overflow-y: auto;">
                    <div id="icsPreview" style="width: 100%; max-width: 400px; border: 1px solid #ccc; padding: 10px; margin-top: 20px;">
                    <h3>.ics File Preview:</h3>
                    <pre style="font-size: 0.8em" id="icsPreviewContent"></pre>
                    </div>
                </div>                
            </div>


        </div>
    </div>
  
    
    <div id='page6' class='page'>
        <p class='pageTitle'>When should Chitkar start sending out your newsletter?</p>
        <p class='pageExplain'>The default setting - now - or a time in the past means Chitkar will start sending as soon as this newsletter is queued.</p>
        <input type='button' value='<< Prev' class='prevBtn'>
        <input type='button' value='Next >>' class='nextBtn'>
        <div class='pageContent'>
	        <div class="row">
            <?php if(empty($model->sendDate)) {
                    $model->sendDate=date('Y-m-d H:i:00', time()+300);
            } ?>
		        <label for="Newsletters_sendDate">Embargo until</label>
                <?php $this->widget('application.extensions.timepicker.timepicker', array(
                          'model'=>$model,
                          'name'=>'sendDate',
                          'options'=>array('defaultValue'=> 'hi'),
                          ));
                ?>
		        <?php echo $form->error($model,'sendDate'); ?>
	        </div>
        </div> 
    </div>
    
    <div id='page7' class='page'>
        <p class='pageTitle'>Enter notification emails</p>
        <p class='pageExplain'>A copy of the newsletter will be sent to the following email addresses once all outgoing messages have been sent.<br /><i>NOTE: seperate multiple emails with a semi-colon</i></p>
        <input type='button' value='<< Prev' class='prevBtn'>
        <input type='button' value='Next >>' class='nextBtn'>
        <?php
            $message=""; 
            if(Yii::app()->dbConfig->getValue('default_notifications') && !$model->notifications && !$model->id) {
                $model->notifications=Yii::app()->dbConfig->getValue('default_notifications');
            }
        ?>
        <div class='pageContent'>
            <div class="row">
                <?php echo $form->labelEx($model,'notifications'); ?>
                <?php echo $form->textField($model, 'notifications', array('size'=>80)); ?>
                <?php echo $form->error($model, 'notifications'); ?>
                <?php echo $message ?>
            </div>
        </div>
    </div>
    
    <div id='page8' class='page'>
        <p class='pageTitle'>Choose your other settings</p>
        <p class='pageExplain'>Choose additional settings here, such as whether or not to archive the newsletter, whether to track how many people read it, how many times links within it are clicked and how many emails bounce.<br /><i>NOTE: Not all yet functional</i></p>
        <input type='button' value='<< Prev' class='prevBtn'>
        <div class='pageContent'>
        <?php if(empty($model->trackReads)) {
            if(Yii::app()->dbConfig->getValue('default_trackReads')) {
                $model->trackReads=Yii::app()->dbConfig->getValue("default_trackreads");
            }
        } 
        ?>
            <div class="row" style='float: left; width: 150px'>
		        <?php echo $form->labelEx($model,'trackReads'); ?>
		        <?php echo $form->dropDownList($model, 'trackReads', $yesNo) ?>
                <?php echo $form->error($model,'trackReads'); ?>
	        </div>

        <?php if(empty($model->trackLinks)) {
            if(Yii::app()->dbConfig->getValue('default_trackLinks')) {
                $model->trackLinks=Yii::app()->dbConfig->getValue("default_trackLinks");
            }
        } 
        ?>
	        <div class="row" style='float: left; width: 150px'>
		        <?php echo $form->labelEx($model,'trackLinks'); ?>
		        <?php echo $form->dropDownList($model, 'trackLinks', $yesNo) ?>
                <?php echo $form->error($model,'trackLinks'); ?>
	        </div>

        <?php if(empty($model->trackBounces)) {
            if(Yii::app()->dbConfig->getValue('default_trackBounces')) {
                $model->trackBounces=Yii::app()->dbConfig->getValue("default_trackBounces");
            }
        } 
        ?>
 	        <div class="row" style='float: left; width: 150px'>
		        <?php echo $form->labelEx($model,'trackBounces'); ?>
		        <?php echo $form->dropDownList($model, 'trackBounces', $yesNo) ?>
                <?php echo $form->error($model,'trackBounces'); ?>
	        </div>
        <?php if(empty($model->archive)) {
            if(Yii::app()->dbConfig->getValue('default_archive')) {
                $model->archive=Yii::app()->dbConfig->getValue("default_archive");
            }
        } 
        ?>
            <div class="row" style='float: left; width: 150px'>
                <?php echo $form->labelEx($model,'archive'); ?>
                <?php echo $form->dropDownList($model, 'archive', $yesNo) ?>
                <?php echo $form->error($model,'archive'); ?>
            </div>
            <div style='clear: both'></div>

            <div class="row buttons">
                <?php echo CHtml::submitButton($model->isNewRecord ? 'Save Newsletter' : 'Save'); ?>
            </div>
        </div>
    </div>
    <?php
/**
* 
* THE FOLLOWING DIVS DISPLAY THE REQUIRED PARTS OF THE RECIPIENT LISTS GENERATION TOOL
* 
* 
*/
    ?>
    <div id="dialog-confirm" title="Change the existing SQL?">
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
        If you choose 'Change SQL', any manually created SQL modifications will be lost.</p>
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
        If you choose 'Use original SQL' any changes you've mode to the filters on this page will be ignored.</p>
    </div>
    <div id='testsql' title='Test SQL'>
        <div id='testsql_results' class='testsql'>
        </div>
    </div>
    <div id='buildsql' title='Build SQL'>
        <div class='pageContent'>
            <div class="row">
            <p class='pageExplain'>Enter your field values here.</p>
            <?php
                if(!empty($model->recipientValues)) {
                    $rvalues=explode(";", $model->recipientValues);
                    $recipientvaluecontents=array();
                    foreach($rvalues as $rvalue) {
                        list($name, $val)=explode(":", $rvalue);
                        $recipientvaluecontents[$name]=$val;
                    }
                }
                foreach($fields as $field=>$properties) {
                    //$fieldnames[]=$field;
                    $fieldnames[]=$properties['fieldname'];
                    $tables[]=$properties['tablename'];
                    $joins[]=$properties['join'];
                    $fieldjoins[]=$properties['fieldjoins'];
                    $inputcontent="";
                    if(isset($recipientvaluecontents[$field])) {$inputcontent = $recipientvaluecontents[$field];}
                    echo "<div class='sqlTableCol1'>".$properties['displayname']."</div>";
                    echo "<div class='sqlTableCol2'><input type='text' size='20' name='{$properties['fieldname']}' id='{$properties['tablename']}.{$properties['fieldname']}' class='edb_fields' value='$inputcontent' /></div>\n";
                    echo "<div style='clear: both'></div>";
                }
                //$fieldjoins=array();
                //$sqljoins=array();
                echo "<input type='hidden' id='edb_fieldnames' value='".implode(",", $fieldnames)."' />\n";
                echo "<input type='hidden' id='edb_fieldjoins' value='".implode(",", $fieldjoins)."' />\n";
                echo "<input type='hidden' id='edb_tables' value='".implode(",", $tables)."' />\n"; 
                echo "<input type='hidden' id='edb_joins' value='".implode(",", $joins)."' />\n";
                echo "<input type='hidden' id='edb_sql_joins' value='".implode(",", $starters['joins'])."' />\n";
                echo "<input type='hidden' id='edb_sql_wheres' value=\"".implode(",", $starters['wheres'])."\" />\n";
                echo "<input type='hidden' id='edb_sql_froms' value=\"".implode(",", $starters['froms'])."\" />\n";
                echo "<input type='hidden' id='edb_sql_selects' value=\"".implode(",", $starters['selects'])."\" />\n";
            ?>
            </div>
        </div>
    </div>
    <input type='hidden' id='sqlOK' value='0' />
    <input type='hidden' id='summaryname' />
    <input type='hidden' id='summarycount' value='' />
    <div id='summary'>
        <div id='summarycount' style='display: none'></div>
    </div>
<?php $this->endWidget(); ?>

</div><!-- form -->

<?php
function convertICalDateToDateTime($icalDate) {
    // Extract the components from the iCalendar date
    $year = substr($icalDate, 0, 4);
    $month = substr($icalDate, 4, 2);
    $day = substr($icalDate, 6, 2);
    $hour = substr($icalDate, 9, 2);
    $minute = substr($icalDate, 11, 2);
    $second = substr($icalDate, 13, 2);

    // Construct a date string
    $dateString = "{$year}-{$month}-{$day} {$hour}:{$minute}:{$second}";
    // Convert to Unix timestamp and then to desired format
    $timestamp = strtotime($dateString ); // Append ' UTC' to interpret as UTC time
    if ($timestamp === false) {
        error_log("Failed to convert icalDate to timestamp: $icalDate");
        return $icalDate;
    }
    // Convert the timestamp to 'Y-m-d H:i:s' in the desired timezone
    $convertedDate = date('Y-m-d H:i:s', $timestamp);
    return $convertedDate;
}

function convertTimezone($dateString, $timezone) {
    $date = new DateTime($dateString, new DateTimeZone('UTC'));
    $date->setTimezone(new DateTimeZone($timezone));
    return $date->format('Y-m-d H:i:s');
}


?>