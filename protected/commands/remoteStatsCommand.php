<?php
  class remoteStatsCommand extends CConsoleCommand{
    public function run($args) {
        //error_reporting(E_ALL);
        echo "Running";
        //TODO: MOve these settings to config
        $ftp_server=Yii::app()->dbConfig->getValue('ftp_server');
        $ftp_user_name=Yii::app()->dbConfig->getValue('ftp_username');
        $ftp_user_pass=Yii::app()->dbConfig->getValue('ftp_password');
        $ftp_file_name=Yii::app()->dbConfig->getValue('ftp_read_file');
        $ftp_file_location=Yii::app()->dbConfig->getValue('ftp_read_file_location');
        $local_temp_location=Yii::app()->dbConfig->getValue('local_temp_location');
        $discardlog="";
        $successlog="";
        
        $local_file=$local_temp_location."/reads.ctk";
        
        //Prevent multiple instances
        /** STOP SCRIPT RUNNING TWICE **/
        $statslock=$local_temp_location."/statslock.txt";
        if(file_exists($statslock) && file_get_contents($statslock) > (time() - 60)){
            $locktime=file_get_contents($statslock);
            $lockinfo="Locked until ".date("H:i:s", $locktime+60). " - current time ".date("H:i:s"). " and using ".date("H:i:s", (time()-60));
            die("Should not run! ($lockinfo)");
        }
        // This lock will be removed once the script has stopped running
        // 30 minutes should be sufficient for a REALLY REALLY BIG stats file        
        
        $success=0;
        $count=0;
        $saves=0;
        $discards=0;
        $errors="";
        $time=time();
        
        echo "Connecting to ftp ($ftp_server)";
        $conn_id=ftp_connect($ftp_server);
        if(!$conn_id) {
            $errors.= "FTP connection failed attempting to connect to $ftp_server at step 1";
        } else if($conn_id) {
            if(!$login_result=ftp_login($conn_id, $ftp_user_name, $ftp_user_pass)) {
                $errors.= "FTP login failed attempting to login as $ftp_user_name";
            } else {
                file_put_contents($statslock, time()+1800); //Lock the system for 30 minutes, to allow for very long processes. 

                echo "\r\nDownloading reads file ($ftp_file_location/$ftp_file_name) to $local_file...<br />\r\n";
                if(@$download=ftp_get($conn_id, $local_file, $ftp_file_location."/".$ftp_file_name, FTP_ASCII)) {
                    //echo "Reads file is downloaded.<br />";
                    //Now read the file and process it
                    $handle=fopen($local_file, "r");
                    $contents=fread($handle, filesize($local_file));
                    fclose($handle);
                    
                    $reads=explode(";", $contents);
                    $count=count($reads);
                    //echo "Processing $count records<br />\n";
                    foreach($reads as $record) {
                        if(!empty($record)) {
                            //echo "Doing record $record<br />\n";
                            //print_r($record);
                            list($datestamp, $newsletterid, $recipientid, $filename)=explode(":", $record);    
                            $outgoing=Outgoings::model()->find("recipientId = :recipid AND newslettersId = :newsid", array(":recipid"=>$recipientid, ":newsid"=>$newsletterid));
                            if($outgoing && $outgoing->read != 1) {
                                $outgoing->read=1; //Mark this entry as read
                                $outgoing->readTime=date("Y-m-d H:i:s", $datestamp); //Save the time
                                $outgoing->save(); //Save the change
                                $success = 1;
                                $successlog.="[$newsletterid-$recipientid updated]";
                                $saves++;                    
                            } else {
                                $discardlog.="[$newsletterid-$recipientid ignored]";
                                $discards++;
                                $success = 1;
                            }
                        } else {
                            $discards++;
                            $discardlog.="[Empty record]";
                        }
                    }
                    
                    //Delete the file on the server
                    if($success==1) {
                        ftp_delete($conn_id, $ftp_file_location."/".$ftp_file_name);
                        $errors.="[FILE DELETED]"; 
                    }            
                } else {
                    $errors.="No remote file to download";
                    //echo $errors;
                }           
                ftp_close($conn_id);
            }
        }  
        $log_file = Yii::app()->dbConfig->getValue('stats_log_file') ? Yii::app()->dbConfig->getValue('stats_log_file') : $local_temp_location.'/remoteStats.log';
        $data = "READS[".date("d/m/Y h:i:s")."] Records:$count Saves:$saves$successlog Discards:$discards$discardlog Errors:$errors\n";
        file_put_contents($log_file, $data, FILE_APPEND);
        










        /**
        * NOW DO THE LINKS FILE
        * 
        * @var mixed
        */
        

        $ftp_file_name=Yii::app()->dbConfig->getValue('ftp_links_file');
        $ftp_file_location=Yii::app()->dbConfig->getValue('ftp_links_file_location');
        $discardlog="";
        $successlog="";

        $local_file=$local_temp_location."/links.ctk";
        
        $success=0;
        $count=0;
        $saves=0;
        $discards=0;
        $errors="";
        $time=time();
        
        
        $conn_id=ftp_connect($ftp_server);
        
        if(!$conn_id) {
            $errors.= "FTP connection failed attempting to connect to $ftp_server at step 2";
            //echo $errors;
            
        } else if ($conn_id){
            $login_result=ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
            if(!$login_result) {
                $errors.= "FTP login failed attempting to login as $ftp_user_name";
            } else {
                echo "\r\nDownloading Links file ($ftp_file_location/$ftp_file_name) to $local_file...<br />\r\n";
                if(!@ftp_get($conn_id, $local_file, $ftp_file_location."/".$ftp_file_name, FTP_ASCII)) {
                    //echo "No remote file to download";
                    $errors.="No remote file to download";
                    //echo $errors;
                } else {
                    echo "Links file downloaded!<br />";
                    //Now read the file and process it
                    $handle=fopen($local_file, "r");
                    if(filesize($local_file) > 0) {
                        $contents=fread($handle, filesize($local_file));
                        fclose($handle);
                        $reads=explode(";", $contents);
                        //print_r($reads);
                        $count=count($reads);
                        foreach($reads as $record) {
                            //echo "Doing record: ".$record."\n";
                            if(!empty($record)) {
                                //print_r($record);
                                $arr=array_pad(explode(':', $record), 4, null);
                                list($datestamp, $newsletterid, $recipientid, $url)=$arr;    
                                $outgoing=Outgoings::model()->find("recipientId = :recipid AND newslettersId = :newsid", array(":recipid"=>$recipientid, ":newsid"=>$newsletterid));
                                if($outgoing && $outgoing->linkUsed != 1) {
                                    $outgoing->linkUsed=1; //Mark this entry as read
                                    $outgoing->linkUsedTime=date("Y-m-d H:i:s", $datestamp); //Save the time
                                    $outgoing->link=$url;
                                    if($outgoing->read != 1) { //Obviously, the email has been read, so make sure it's marked as such
                                        $outgoing->read = 1;
                                        $outgoing->readTime = date("Y-m-d H:i:s", $datestamp);
                                        $errors.="Read updated as well";
                                    }
                                    $outgoing->save(); //Save the change
                                    $success = 1;
                                    $successlog.="[$newsletterid-$recipientid updated]";
                                    $saves++;                    
                                } else {
                                    $discardlog.="[$newsletterid-$recipientid ignored]";
                                    $discards++;
                                    $success = 1;
                                }
                            } else {
                                $discards++;
                                $discardlog.="[Empty or unmatchable record]";
                            }
                        }
                        
                        
                    } else {
                        //File size was 0... all OK, proceed
                        $success=1;
                    }
                    //Delete the file on the server
                    if($success==1) {
                        ftp_delete($conn_id, $ftp_file_location."/".$ftp_file_name);
                        $errors.="[FILE DELETED]";
                        echo "Links.ctk File on server deleted"; 
                    }            
                }           
                ftp_close($conn_id);
            }
        }      
        $log_file = Yii::app()->dbConfig->getValue('stats_log_file') ? Yii::app()->dbConfig->getValue('stats_log_file') : $local_temp_location.'/remoteStats.log';
        $data = "LINKS[".date("d/m/Y h:i:s")."] Records:$count Saves:$saves$successlog Discards:$discards$discardlog Errors:$errors\n";
        file_put_contents($log_file, $data, FILE_APPEND);         
        
        
        /**
        * Now do the unsubscribes file
        */
        $ftp_file_name=Yii::app()->dbConfig->getValue('ftp_unsubscribe_file');
        $ftp_file_location=Yii::app()->dbConfig->getValue('ftp_unsubscribe_file_location');
        $discardlog="";
        $successlog="";
        
        $local_file=$local_temp_location."/unsubscribe.ctk";
        
        $success=0;
        $count=0;
        $saves=0;
        $discards=0;
        $errors="";
        $time=time();
        
        $conn_id=ftp_connect($ftp_server);
        
        if(!$conn_id) {
            $errors .= "FTP connection failed attempting to connect to $ftp_server at step 3";    
        } else {
            $login_result=ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
            if(!$login_result) {
                $errors .= "FTP Login failed attempting to login as $ftp_user_name  when collecting unsubscribe information.";
            } else {
                //echo "Downloading unsubscribe file...<br />";
                if(@$download=ftp_get($conn_id, $local_file, $ftp_file_location."/".$ftp_file_name, FTP_ASCII)) {
                    //Now read the file and process it
                    $handle=fopen($local_file, "r");
                    $contents=fread($handle, filesize($local_file));
                    fclose($handle);
                    $reads=explode(";", $contents);
                    $count=count($reads);
                    $unsubscribe=new Unsubscribes;

                    foreach($reads as $record) {
                        if(!empty($record)) {
                            list($datestamp, $recipientid, $email, $reasons) = explode(":", $record);
                            if(!empty($recipient)) {
                                $unsubscribe->email=$email;
                                $unsubscribe->recipientId=$recipient;
                                $unsubscribe->created=date("Y-m-d h:i:s", $datestamp);
                                $unsubscribe->reasons=$reasons;
                                if($unsubscribe->save()) {
                                    
                                } else {
                                    echo $unsubscribe->getErrors();
                                }
                                $success = 1;
                                $successlog . "[$email added to unsubscribes]";
                            }
                        }
                    }
                    
                    //Delete the file on the server
                    if($success==1) {
                        ftp_delete($conn_id, $ftp_file_location."/".$ftp_file_name);
                        $errors.="[FILE DELETED]";
                    }
                } else {
                    $errors .= "No remote file to download ($ftp_file_location/$ftp_file_name)";
                }
                ftp_close($conn_id);
            }
        }
        $log_file = Yii::app()->dbConfig->getValue('stats_log_file') ? Yii::app()->dbConfig->getValue('stats_log_file') : $local_temp_location.'/remoteStats.log';
        $data = "UNSUBSCRIBES[".date("d/m/Y h:i:s")."] Records: $count Saves:$saves$successlog Errors:$errors\n";
        file_put_contents($log_file, $data, FILE_APPEND);
        file_put_contents($statslock, time()); //unlock the system so script can run again
        
        echo $errors;
                  
    } //End RUN function
  } //End Class
?>
