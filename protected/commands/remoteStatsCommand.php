<?php
  class remoteStatsCommand extends CConsoleCommand{
    public function run($args) {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        echo "Running...\r\n";
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
            $lockinfo="Locked with $statslock until ".date("H:i:s", $locktime+60). " - current time ".date("H:i:s"). " and using ".date("H:i:s", (time()-60));
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



        /** 
         * Checking the reads file
         */
        
        echo "Connecting to ftp ($ftp_server)...";
        $conn_id=ftp_connect($ftp_server);
        if(!$conn_id) {
            $errors.= "FTP connection failed attempting to connect to $ftp_server at step 1";
        } else if($conn_id) {
            echo "connected.\r\n\r\n";
            //Now disconnect
            //ftp_close($conn_id);
            //Enable passive mode

            if(!$login_result=ftp_login($conn_id, $ftp_user_name, $ftp_user_pass)) {
                $errors.= "FTP login failed attempting to login as $ftp_user_name";
            } else {
                echo "Login successful for reads file.\r\n";
                if(!ftp_pasv($conn_id, true)) {
                    echo "Failed to enable passive mode.\r\n";
                    $debug_response = ftp_raw($conn_id, 'STAT');
                    print_r($debug_response);                
                    if(!ftp_pasv($conn_id, false)) {
                        echo "Failed to enable active mode.\r\n";
                    }
                } else {
                    echo "Passive mode enabled\r\n";
                }

                file_put_contents($statslock, time()+1800); //Lock the system for 30 minutes, to allow for very long processes. 
                $startTime = time();




                echo " - Downloading reads file ($ftp_file_location/$ftp_file_name) to $local_file...\r\n";
                if(!$download=ftp_get($conn_id, $local_file, $ftp_file_location."/".$ftp_file_name, FTP_ASCII))  {
                    $errors.="No remote file to download (".$ftp_file_location.")\r\n\r\n";
                    echo $errors;
                } else {
                    echo "Reads file is downloaded.<br />\r\n";
                    //Now read the file and process it
                    $handle=fopen($local_file, "r");
                    $contents=fread($handle, filesize($local_file));
                    fclose($handle);

                    // Remove any NULL characters (hex 00)
                    $contents = str_replace("\x00", '', $contents);     
                    
                    //Save the $contents to a new file for checking
                    $temp_file = $local_temp_location . '/reads_temp.ctk';

                    //Write to $contents to $temp_file (even if $contents is empty) and make the file a+rwx - but don't check or create the directory
                    //file_put_contents($temp_file, $contents);
                    //chmod($temp_file, 0777);

                    

                    //die("Written contents to $temp_file for debugging purposes. Please check the file and remove this line from the code.");

                    
                    $reads=explode(";", $contents);
                    $count=count($reads);
                    //echo "Processing $count records<br />\n"; die();
                    foreach($reads as $record) {
                        if(!empty($record)) {
                            //echo "Doing record $record<br />\r\n\n";
                            //print_r($record);
                            list($datestamp, $newsletterid, $recipientid, $filename)=explode(":", $record);    
                            $outgoing=Outgoings::model()->find("recipientId = :recipid AND newslettersId = :newsid", array(":recipid"=>$recipientid, ":newsid"=>$newsletterid));
                            if($outgoing && $outgoing->read != 1) {
                                $outgoing->read=1; //Mark this entry as read
                                $outgoing->readTime=date("Y-m-d H:i:s", $datestamp); //Save the time
                                $outgoing->save(); //Save the change
                                $success = 1;
                                $successlog.="[$newsletterid-$recipientid read updated] ";
                                $saves++;                    
                            } else {
                                $discardlog.="[$newsletterid-$recipientid read ignored] ";
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
                        echo "Finished with the reads file successfully! Deleting from server.\r\n\r\n";
                        ftp_delete($conn_id, $ftp_file_location."/".$ftp_file_name);
                        $errors.="[FILE DELETED]"; 
                    }            
                }           
                ftp_close($conn_id);
            }
        }  
        $log_file = Yii::app()->dbConfig->getValue('stats_log_file') ? Yii::app()->dbConfig->getValue('stats_log_file') : $local_temp_location.'/remoteStats.log';
        $data = "READS[".date("d/m/Y h:i:s")."] Records:$count Saves:$saves\n$successlog\n Discards:$discards\n$discardlog\n Errors:$errors\n\n\n";
        file_put_contents($log_file, $data, FILE_APPEND);
        
        //die("Debugging, die here");









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
        
        echo "Connecting to ftp server...";
        $conn_id=ftp_connect($ftp_server);
        
        if(!$conn_id) {
            $errors.= "FTP connection failed attempting to connect to $ftp_server at step 2\r\n";
            //echo $errors;
            
        } else if ($conn_id) {
            $login_result=ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
            if(!$login_result) {
                $errors.= "FTP login failed attempting to login as $ftp_user_name \r\n\r\n";
            } else {
                echo "Login successful for links file!\r\n\r\n";
                if(!ftp_pasv($conn_id, true)) {
                    echo "Failed to enable passive mode.\r\n";
                    $debug_response = ftp_raw($conn_id, 'STAT');
                    print_r($debug_response);                
                    if(!ftp_pasv($conn_id, false)) {
                        echo "Failed to enable active mode.\r\n";
                    }
                } else {
                    echo "Passive mode enabled\r\n";
                }

                echo "Downloading Links file ($ftp_file_location/$ftp_file_name) to $local_file...\r\n\r\n";
                if(@$download=ftp_get($conn_id, $local_file, $ftp_file_location."/".$ftp_file_name, FTP_ASCII)) {
                    echo "Links file downloaded!<br />";
                    
                    //Now read the file and process it
                    $handle=fopen($local_file, "r");
                    if(filesize($local_file) > 0) {
                        $contents=fread($handle, filesize($local_file));
                        fclose($handle);

                        // Remove any NULL characters (hex 00)
                        $contents = str_replace("\x00", '', $contents);     

                        $reads=explode(";", $contents);
                        //print_r($reads);
                        $count=count($reads);
                        $readprocesscount=0;
                        foreach($reads as $record) {
                            $readprocesscount++;
                            echo "Doing record: ".$record."<br />\n".$readprocesscount."of ".$count."\n"; 
                            //die();
                            if(!empty($record)) {
                                print_r($record);
                                $arr=array_pad(explode(':', $record), 4, null);
                                list($datestamp, $newsletterid, $recipientid, $url)=$arr;    

                                if(is_numeric($newsletterid) && is_numeric($recipientid)) {
                                    $outgoing=Outgoings::model()->find("recipientId = :recipid AND newslettersId = :newsid", array(":recipid"=>$recipientid, ":newsid"=>$newsletterid));
                                    if($outgoing && $outgoing->linkUsed != 1) {
                                        $outgoing->linkUsed=1; //Mark this link as used
                                        //Ensure $datestamp is a valid integer by converting it to an integer
                                        $datestamp=intval($datestamp);

                                        $outgoing->linkUsedTime=date("Y-m-d H:i:s", $datestamp); //Save the time
                                        $outgoing->link=$url;
                                        if($outgoing->read != 1) { //Obviously, if the link has been used, then the email has also been read, so make sure it's marked as such
                                            $outgoing->read = 1;
                                            $outgoing->readTime = date("Y-m-d H:i:s", $datestamp);
                                            $errors.="Read updated as well";
                                        }
                                        $outgoing->save(); //Save the change
                                        $success = 1;
                                        $successlog.="[$newsletterid-$recipientid link updated]";
                                        $saves++;                    
                                    } else {
                                        $discardlog.="[$newsletterid-$recipientid link ignored, already recorded]";
                                        $discards++;
                                        $success = 1;
                                    }                                    
                                } else {
                                    //The values don't pass a sanity test. Ignore
                                    $displaynid=isset($newsletterid) ? $newsletterid : "[unknown]";
                                    $displayrid=isset($recipientid) ? $recipientid : "[unknown]";
                                    $discardlog.="[".htmlspecialchars($displaynid)."-".htmlspecialchars($displayrid)." link ignored, possibly injection attack, or maybe someone using the CC internal email] ";
                                    $discards++;
                                    $success = 1;
                                }

                                if($readprocesscount > 4 && 1==0) {
                                    echo "Success log: \n";
                                    print_r($successlog);

                                    echo "Discard log: \n";
                                    print_r($discardlog);
                                    die("\nTesting, die here");
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
                } else {
                    //echo "No remote file to download";
                    $errors.="No links file to download\r\n\r\n";
                    echo $errors;
                }
                ftp_close($conn_id);
            }
        }      
        $log_file = Yii::app()->dbConfig->getValue('stats_log_file') ? Yii::app()->dbConfig->getValue('stats_log_file') : $local_temp_location.'/remoteStats.log';
        $data = "LINKS[".date("d/m/Y h:i:s")."] Records:$count Saves:$saves\n$successlog\n Discards:$discards\n$discardlog\n Errors:$errors\n\n\n";
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
        
        echo "Connecting to $ftp_server...";
        $conn_id=ftp_connect($ftp_server);
        
        if(!$conn_id) {
            $errors .= "FTP connection failed attempting to connect to $ftp_server at step 3";    
        } else {
            $login_result=ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
            if(!$login_result) {
                $errors .= "FTP Login failed attempting to login as $ftp_user_name  when collecting unsubscribe information.";
            } else {
                echo "Login successful for unsubscribes file!\r\n\r\n";
                if(!ftp_pasv($conn_id, true)) {
                    echo "Failed to enable passive mode.\r\n";
                    $debug_response = ftp_raw($conn_id, 'STAT');
                    print_r($debug_response);                
                    if(!ftp_pasv($conn_id, false)) {
                        echo "Failed to enable active mode.\r\n";
                    }
                } else {
                    echo "Passive mode enabled\r\n";
                }                
                echo "Downloading unsubscribe file...\r\n\r\n";
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
                    $errors .= "No remote file to download ($ftp_file_location/$ftp_file_name)\r\n\r\n";
                }
                ftp_close($conn_id);
            }
        }
        $log_file = Yii::app()->dbConfig->getValue('stats_log_file') ? Yii::app()->dbConfig->getValue('stats_log_file') : $local_temp_location.'/remoteStats.log';
        $data = "UNSUBSCRIBES[".date("d/m/Y h:i:s")."] Records: $count Saves:$saves$successlog Errors:$errors\n\n\n";
        file_put_contents($log_file, $data, FILE_APPEND);
        echo "Updated statslock with ".time();
        file_put_contents($statslock, time()); //unlock the system so script can run again
        
        echo $errors;
                  
    } //End RUN function
  } //End Class
?>
