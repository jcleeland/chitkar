<?php
require_once(Yii::getPathOfAlias('application.extensions.PHPMailer') . '/PHPMailer.php');
require_once(Yii::getPathOfAlias('application.extensions.PHPMailer') . '/SMTP.php');
require_once(Yii::getPathOfAlias('application.extensions.PHPMailer') . '/Exception.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class queueCommand extends CConsoleCommand{
    public function run($args) {
        //die();
        /**
        * 
        */
        $now=date("Y-m-d H:i:s");
        $basedir=Yii::app()->basePath;
        $queuelock=$basedir."/../tmp/queuelock.txt";
        $dbfail=$basedir."/../tmp/dbfailure.ctk";
        $databasefailure=0;
        $failuredata="";
        
        
        /** STOP SCRIPT RUNNING TWICE **/
        if(file_exists($queuelock) && file_get_contents($queuelock) > (time() - 60)){
            $locktime=file_get_contents($queuelock);
            $lockinfo="Locked until ".date("H:i:s", $locktime+60). " - current time ".date("H:i:s");
            $orderfile=fopen($basedir."/../tmp/outgoing_order.log", "a");
            fwrite($orderfile, "------------------------------------\r\n");
            fwrite($orderfile, "Did not run - queuelock in place (".date("Y-m-d h:i:s", strtotime($queuelock)).")");   
            fclose($orderfile);
            die("Should not run! ($lockinfo)");
        }
        
        $queuelocktime=Yii::app()->dbConfig->getValue('smtp_queuelock_time') ? Yii::app()->dbConfig->getValue('smtp_queuelock_time') : 10800;
        file_put_contents($queuelock, time()+$queuelocktime); //Lock the system for x hours, to allow for very long queues. This lock will be removed once the script has stopped running
        // 3 hours should be sufficient for 18000 emails sent at a relatively slow rate of 0.6 seconds per email
        /** STOP SCRIPT RUNNING TWICE **/
        $outgoingsemailthrottle=Yii::app()->dbConfig->getValue('smtp_outgoings_email_throttle') ? Yii::app()->dbConfig->getValue('smtp_outgoings_email_throttle') : 0;
        $maxoutgoings=($outgoingsemailthrottle > 0) ? intval(150/($outgoingsemailthrottle/1000000)) : 1000000;
        
        $jobs=Outgoings::model()->findAll('sendDate <:now AND sent != 1 AND sendFailures < 3 ORDER BY id ASC LIMIT '.$maxoutgoings, array(':now'=>$now));

        $log_file = Yii::app()->dbConfig->getValue('queue_log_file') ? Yii::app()->dbConfig->getValue('queue_log_file') : '/var/www/chitkar/tmp/queue.log';
        $data="[$now]\n";
        $sentitems=0;
        $faileditems=0;
        

                
        $fromemail=Yii::app()->dbConfig->getValue('email_from');
        $fromname=Yii::app()->dbConfig->getValue('email_fromname');
        $savelog=Yii::app()->dbConfig->getValue('email_logging');
        

        
        $mail = new PHPMailer();
        //$mail = new PHPMailer\PHPMailer();
        $mail->IsSMTP();
        $mail->Mailer = "smtp";
        $mail->Host=Yii::app()->dbConfig->getValue('smtp_server');
        //$mail->SMTPAuth = true;
        $mail->Username = Yii::app()->dbConfig->getValue('smtp_username');
        $mail->Password = Yii::app()->dbConfig->getValue('smtp_password');
        $mail->setFrom($fromemail, $fromname);
        $mail->isHTML(true);
        //$mail->SMTPDebug=2;
            
        //Quit and notify admin if dbfail file exists
        if(file_exists($dbfail)) {   
            file_put_contents($queuelock, time()); //unlock the system
            $mail->addAddress("jcleeland@cpsuvic.org");
            $mail->Subject="URGENT ERROR:: Send failed due to dbFail file.";
            $mail->Body="The Chitkar queue processing is currently disabled due to the presence of a dbFail file. This could have been manually set, or caused by a database failure. You should visit Chitkar admin and investigate the cause. No emails are being sent.";
            $mail->send();
            file_put_contents($log_file, "[".date("Y-m-d H:i:s")."]\nQueue process suspended due to presence of dbfail file.\n-------------------------------------", FILE_APPEND);
            die("Cannot run until $dbfail is removed.");
        }
        
        $currentnewsletter=null;
        $orderfile=fopen($basedir."/../tmp/outgoing_order.log", "a");
        fwrite($orderfile, "------------------------------------\r\n");   
        fwrite($orderfile, "Sending maximum $maxoutgoings emails\r\n"); 
        $ii=0;
        foreach ($jobs as $job) {
            $ii++;

            //Clear all the mail settings from the last email so you aren't repeating yourself!
            //$mail->clearLayout();   //Old PHPMailer 5 command
            // Clear recipients  // Clear attachments  // Clear reply-to addresses   // Clear custom headers  // Clear CC addresses  // Reset subject, body, and alternative body
            $mail->clearAllRecipients();
            $mail->clearAttachments();
            $mail->clearReplyTos();
            $mail->clearCustomHeaders();
            $mail->clearCCs();
            $mail->Subject = '';
            $mail->Body = '';
            $mail->AltBody = '';            
            
            usleep($outgoingsemailthrottle);
            //echo "Writing file";
            fwrite($orderfile, $ii."/".$maxoutgoings.": ".$job->newslettersId."->".$job->email."(".$job->id.") @ ".date("Y-m-d h:i:s")."\r\n");
            $data .= "\n   Sending new message to ".$job->email." ";
            //echo "Getting newsletter";
            $newsletter=Newsletters::model()->find('id=:newslettersId', array(':newslettersId'=>$job->newslettersId));
            if($newsletter->trackReads==1) {
                $emailcontent=str_replace("{RID}", $job->recipientId, $newsletter->completed_html);
            } else {
                $emailcontent=$newsletter->completed_html;
            }
            //Do personalisation stuff
            $emailcontent=str_replace("{MEMBER}", $job->recipientId, $emailcontent);
            //$emailcontent=str_replace("{FIRSTNAME}", $job->)
            //echo "\r\n<br />THE DATA<br />\r\n";
            //print_r($job);
            if(!empty($job->data)) {
                //echo "Doing personalisations";
                $datadata=json_decode($job->data);
                //Find the names of fields available
                $dataarray=get_object_vars($datadata);
                //print_r($dataarray);
                
                //Iterate through the fields available & replace the names with data
                foreach($dataarray as $dkey=>$dvar) {
                    //echo "Checking form fields for $dkey\r\n<br />";
                    $emailcontent=str_replace("{".$dkey."}", $dvar, $emailcontent);    
                }
                
            }
            
            //echo "Setting up mail message";
            $mail->Subject=$newsletter->subject;
            $mail->Body=$emailcontent;
            //$mail->setTo("jcleeland@cpsuvic.org");
            //$mail->setTo(array("jcleeland@cpsuvic.org"=>$job->email));
            $mail->addAddress($job->email);
            //print_r($mail);
            //echo "Sending mail";
            //sleep(2);
            
            if($mail->send()) {
                //echo "Mail was sent";
                $sentitems++;
                $data .= "  - Success.";
                $job->dateSent=$now;
                $job->sent = 1;
                if(empty($job->recipientId)) $job->recipientId="F".substr(md5(uniqid(mt_rand(), true)), 0, 8); //Generate a random 8 character string
                if(empty($job->recipientListsId)) $job->recipientListsId=0;
                if ($job->save()) {
                    $data .= " Database updated.\n";
                } else {
                    $dberrors=print_r($job->getErrors(), 1);
                    $data .= " Database did not update.\n";
                    $data .= " Error message: ".$dberrors."\n";
                    
                    // TODO: Place halt on emailing, send notification to owner
                    // INCLUDE button to Enable/Disable outgoing emails
                    $databasefailure=1;
                    $failuredata .= "Error saving db update to ".$job->email." for newsletter ".$job->newslettersId." with database error '".$dberrors."'.<br />\r\n";
                    //$failuredata .= "JOB DATA: ";
                    //$failuredata .= print_r($job, true);
                }
                
            } else {
                //echo "It failed";
                $faileditems++;
                $smtperror=$mail->ErrorInfo;
                $data .= "  - Failure. [SMTP ERROR REPORT: ";
                $data .= "    ".$smtperror."]\n";
                $job->sendFailures=$job->sendFailures + 1;
                $job->sendFailureText=$mail->ErrorInfo;
                if ($job->save()) {
                    $data .= "Database updated";
                } else {
                    $dberrors=print_r($job->ErrorInfo, 1);
                    $databasefailure=1;
                    $failuredata .= "Error saving db update to ".$job->email." for newsletter ".$job->newslettersId." with database error '".$dberrors."''.\n";         
                    $data .= "Error saving db update for newsletter ".$job->newslettersId." with database error '".$dberrors."'.\n";
                }
                if(trim($smtperror) == "SMTP Connect() failed.") {
                    $databasefailure=1;
                    $failuredata .= "\nQueue system halted for SMTP Connection Error ($smtperror)\n";
                }
            } 
        }
        //Clear all the mail settings from the last email so you aren't repeating yourself!
        //$mail->clearLayout();   //Old PHPMailer 5 command
        // Clear recipients  // Clear attachments  // Clear reply-to addresses   // Clear custom headers  // Clear CC addresses  // Reset subject, body, and alternative body
        $mail->clearAllRecipients();
        $mail->clearAttachments();
        $mail->clearReplyTos();
        $mail->clearCustomHeaders();
        $mail->clearCCs();
        $mail->Subject = '';
        $mail->Body = '';
        $mail->AltBody = '';  
        
        
        //Update completed newsletters
        $queued=Newsletters::model()->findAll("queued = 1 AND completed <> 1");
        foreach($queued as $queue) {
            $finished=Outgoings::model()->findAll("newslettersId=".$queue->id." AND (sent <> 1 AND sendFailures < 3)");
            if(empty($finished)) {
                //Queue is complete, newsletter should be marked done
                $queue->completed=1;
                $queue->save();
                //Now we should send out notification emails to those on the notifications list
                //TODO
                if(!empty($queue->notifications)) {
                    $notices=explode(";",$queue->notifications);
                    $mail->setFrom($fromemail, $fromname);
                    foreach($notices as $noticee) {
                        $mail->Subject="(NOTIFICATION) ".$queue->subject;
                        $mail->Body=$queue->completed_html;
                        $mail->addAddress($noticee);
                        $mail->send();
                    }
                }
            }
        }        
        $data.=$sentitems." of ". count($jobs)." emails sent, ";
        $data.=$faileditems." of ".count($jobs)." emails failed\n";
        $data.="-------------------------------------\n";
        
        if($databasefailure == 1) {
            file_put_contents($dbfail, $failuredata); //Lock the system until checked
        }
        
        if($savelog == 1) {
            file_put_contents($log_file, $data, FILE_APPEND);          
        }
        file_put_contents($queuelock, time()); //unlock the system
        fwrite($orderfile, "--> Updated queuelock\r\n");
        fwrite($orderfile, "---------------------------\r\n\r\n");
        fclose($orderfile);        


    }
    
}
?>
