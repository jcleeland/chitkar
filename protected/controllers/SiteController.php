<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

    
    public function actionStatsdisplay() {
        $starttime=time();
        $subset=isset($_GET['subset']) ? $_GET['subset'] : "newsletterbox";
        $varnames=array(
            "newsletterbox"=>"newsletters",
            "queuedbox"=>"queued",
            "sentbox"=>"sent",
            "readsbox"=>"read",
            "linksbox"=>"linkused"
        );
        $varname=$varnames[$subset];
        
        $todayend=mktime(23,55,59, date("m"), date("d"), date("Y"));
        $days[0]=$todayend;
        for ($i=1; $i<7; $i++ ) {
            $days[$i]=strtotime('-1 day', $todayend);
            $todayend=$days[$i];
        }
        $statistics=array();
        foreach($days as $day) {
            $datekey=date("Y-m-d", $day);
            if(date("Y-m-d") != $datekey) {
                //echo "Doing $varname and date ".$datekey."<br />";
                //Check the statistics database for records
                $todaystats=Statistics::model()->find("`date` = '$datekey'");
                if($todaystats) {
                    switch($varname) {
                        case "newsletters":
                            $statistics[$datekey][$varname]=$todaystats->newslettersQueued;
                            break;
                        case "sent":
                            $statistics[$datekey][$varname]=$todaystats->emailsSent;
                            break;
                        case "queued":
                            $statistics[$datekey][$varname]=$todaystats->emailsQueued;
                            break;
                        case "read":
                            $statistics[$datekey][$varname]=$todaystats->emailsRead;
                            break;
                        case "bounce":
                            $statistics[$datekey][$varname]=$todaystats->emailBounces;
                            break;
                        case "linkused":
                            $statistics[$datekey][$varname]=$todaystats->linksUsed;
                            break;
                    }
                    
                }
            }
            
            
            if(!isset($statistics[$datekey])) {
                if($varname=="sent")   $statistics[$datekey][$varname]=Statistics::model()->countSentByDate($day);
                if($varname=="queued") $statistics[$datekey][$varname]=Statistics::model()->countQueuedByDate($day);
                if($varname=="read")   $statistics[$datekey][$varname]=Statistics::model()->countReadByDate($day);
                if($varname=="bounce") $statistics[$datekey][$varname]=Statistics::model()->countBounceByDate($day);
                if($varname=="linkused")   $statistics[$datekey][$varname]=Statistics::model()->countLinkUsedByDate($day);
                if($varname=="newsletters") $statistics[$datekey]['newsletters']=Statistics::model()->countNewslettersByDate($day);        
            }
            
            if(date("Y-m-d") != $datekey && isset($statistics[$datekey]) && !isset($todaystats)) {
                //It's not for today, and there's no record in the statistics table
                // so save the daily figures for future use
                $stats=new Statistics;
                $stats->date=$datekey;
                if($varname=="sent")   $stats->emailsSent=$statistics[$datekey]['sent'];
                if($varname=="read")   $stats->emailsRead=$statistics[$datekey]['read'];
                if($varname=="queued") $stats->emailsQueued=$statistics[$datakey]['queued'];
                if($varname=="bounce") $stats->emailBounces=$statistics[$datekey]['bounce'];
                if($varname=="linkused")   $stats->linksUsed=$statistics[$datekey]['linkused'];
                if($varname=="newsletter") $stats->newslettersQueued=$statistics[$datekey]['newsletters'];
                $stats->save();
            }
        }

        $statistics=array_reverse($statistics);
        
        $thisweek=array($varname=>0);
        //$thisweek=array("sent"=>0, "queued"=>0, "read"=>0, "linkused"=>0, "newsletters"=>0, "bounce"=>0);
        foreach($statistics as $stat) {
            $thisweek[$varname]+=$stat[$varname];
        }
        
        $thismonth=array();
        $start=date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), 1, date("Y")));
        $finish=date("Y-m-d H:i:s",mktime(23,55,59, date("m"), date("d"), date("Y")));
        if(1==1) {
            if($varname=="sent")   $thismonth[$varname]=Statistics::model()->countSentByDates($start, $finish);
            if($varname=="queued") $thismonth[$varname]=Statistics::model()->countQueuedByDates($start, $finish);
            if($varname=="read")   $thismonth[$varname]=Statistics::model()->countReadByDates($start, $finish);
            if($varname=="bounce") $thismonth[$varname]=Statistics::model()->countBounceByDates($start, $finish);
            if($varname=="linkused")   $thismonth[$varname]=Statistics::model()->countLinkUsedByDates($start, $finish);
            if($varname=="newsletters")$thismonth[$varname]=Statistics::model()->countNewslettersByDates($start, $finish);        
        }
        
        $forever=array();
        $start=date("1970-01-01 00:00:00");
        if(1==1) {
            if($varname=="sent")   $forever[$varname]=Statistics::model()->countSentByDates($start, $finish);
            if($varname=="queued") $forever[$varname]=Statistics::model()->countQueuedByDates($start, $finish);
            if($varname=="read")   $forever[$varname]=Statistics::model()->countReadByDates($start, $finish);
            if($varname=="bounce") $forever[$varname]=Statistics::model()->countBounceByDates($start, $finish);
            if($varname=="linkused") $forever[$varname]=Statistics::model()->countLinkUsedByDates($start, $finish);
            if($varname=="newsletters") $forever[$varname]=Statistics::model()->countNewslettersByDates($start, $finish);        
        }
        
        $recentnews=($subset=="newsletterbox") ? Newsletters::model()->findAll(array('limit'=>25, 'order'=>'created DESC')) : "";
        $recentpub=($subset=="sentbox") ? Newsletters::model()->findAll(array('having'=>'queued=1', 'limit'=>25, 'order'=>'sendDate DESC', )) : "";
        $recentread=($subset=="readsbox") ? $recentread=Outgoings::model()->with('newsletters')->findAll(array('having'=>"`read`=1", 'limit'=>25, 'order'=>'readTime DESC')) : "";
        $recentclick=($subset=="linksbox") ? Outgoings::model()->with('newsletters')->findAll(array('having'=>'linkUsed=1', 'limit'=>25, 'order'=>'linkUsedTime DESC')) : "";

        //echo "<pre style='font-size: 8pt'>"; print_r($recentnews); echo "</pre>";
        $this->renderPartial('_'.$subset, array(            
            'statistics'=>$statistics,
            'thisweek'=>$thisweek,
            'thismonth'=>$thismonth,
            'forever'=>$forever,
            //'forever'=>"na",
            'recentnews'=>$recentnews,
            'recentpub'=>$recentpub,
            'recentread'=>$recentread,
            'recentclick'=>$recentclick), false, true);
    }



	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
        $thetimer[1]=microtime();
        $userid=Yii::app()->user->id ? Yii::app()->user->id : 0;
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
        $todayend=mktime(23,55,59, date("m"), date("d"), date("Y"));
        $days[0]=$todayend;
        for ($i=1; $i<7; $i++ ) {
            $days[$i]=strtotime('-1 day', $todayend);
            $todayend=$days[$i];
        }
        
        $nowhour=mktime(date("H"), 0, 0, date("m"), date("d"), date("Y"));
        
        $starttime=strtotime('-6 hours', $nowhour);
        $nowhour=strtotime('+1 hour', $nowhour);
        $thetimer[2]=microtime();
        $dbCommand = Yii::app()->db->createCommand("SELECT title, count(title) as count 
                                                      FROM outgoings, newsletters 
                                                      WHERE outgoings.newslettersId=newsletters.id 
                                                      AND readTime >='".date("Y-m-d H:i:s", $starttime)."'
                                                      GROUP BY title ORDER BY count DESC");
        $readsummary = $dbCommand->queryAll();

        
        
        
        
        ///////////////////////////////////////////////////////////////////////////////////
        // MY CHITKAR STATISTICS 
        ///////////////////////////////////////////////////////////////////////////////////
        $thetimer[3]=microtime();
        $userpending=Newsletters::model()->findAll("`usersId` = ".$userid." AND queued != 1");
        $thetimer[4]=microtime();
        $usertoday=Newsletters::model()->findAll("`usersId` = ".$userid." AND queued = 1 AND sendDate > '".date("Y-m-d 00:00:00")."'");
        $thetimer[5]=microtime();
        $userlast10=Newsletters::model()->findAll(array('having'=>'usersId='.$userid.' AND queued=1', 'limit'=>10, 'order'=>'sendDate DESC', ));
        
        
        
        
        
        
        
        ///////////////////////////////////////////////////////////////////////////////////
        //TOP CHITKARER STATISTICS
        ///////////////////////////////////////////////////////////////////////////////////
        $thetimer[6]=microtime();
        
        $userModel= new Users();
        $sendDate=date('Y-m-d 00:00:00');
        $topchitter=$userModel->getNewslettersCountData($sendDate);
        /*$tccriteria=new CDBCriteria();
        $tccriteria->select = 'usersId, count(t.id) as countNewsletters';
        $tccriteria->condition = "sendDate > '".date('Y-m-d 00:00:00')."'";
        $tccriteria->group='usersId';
        $tccriteria->order='count(t.id) desc';
        $tccriteria->with='users';
        $topchitter=Newsletters::model()->findAll($tccriteria);   */

        $thetimer[7]=microtime();
        $userModel= new Users();
        $sendDate=date('Y-m-d 00:00:00', time()-60*60*24*7);
        $topwchitter=$userModel->getNewslettersCountData($sendDate);
        /*$tccriteria=new CDBCriteria();
        $tccriteria->select = 'usersId, count(t.id) as countNewsletters';
        $tccriteria->condition = "sendDate > '".date('Y-m-d 00:00:00', time()-60*60*24*7)."'";
        $tccriteria->group='usersId, t.id, users.id, users.username, users.password, users.email, users.firstname, users.lastname, users.can_create, users.can_delete, users.can_control, users.can_admin, users.created, users.modified';
        $tccriteria->order='count(*) desc';
        $tccriteria->with='users';
        $topwchitter=Newsletters::model()->findAll($tccriteria);  */

        $thetimer[8]=microtime(); 
        $userModel= new Users();
        $sendDate='1900-01-01 00:00:00';
        $topfchitter=$userModel->getNewslettersCountData($sendDate);
        /*$tccriteria=new CDBCriteria();
        $tccriteria->select = 'usersId, count(*) as countNewsletters';
        $tccriteria->condition = "sendDate > '1900-01-01 00:00:00'";
        $tccriteria->group='usersId, t.id, users.id, users.username, users.password, users.email, users.firstname, users.lastname, users.can_create, users.can_delete, users.can_control, users.can_admin, users.created, users.modified';
        $tccriteria->order='count(*) desc';
        $tccriteria->with='users';
        $topfchitter=Newsletters::model()->findAll($tccriteria); */
        
        
        
        
        
        
        //////////////////////////////////////////////////////////////////////////////
        // GOOGLE GRAPHS ON FRONT PAGE
        //////////////////////////////////////////////////////////////////////////////
        
        //////////////////////////////////////////
        // CURRENT DAY STATISTICS
        //////////////////////////////////////////
        $nowhour=mktime(date("H"), 0, 0, date("m"), date("d"), date("Y"));
        $nowhour=strtotime('+1 hour', $nowhour);
        for ($i=1; $i<8; $i++) {
            $hours[$i]=array('start'=>strtotime("-1 hour", $nowhour), 'end'=>$nowhour);
            $nowhour=$hours[$i]['start'];
        }

        //For each entry in $hours, gather "Sent", "Reads" and "Links" for Google graph
        // on front page
        $thetimer[9]=microtime();
        $i=90;
        //THIS FUNCTION IS A TIME KILLER - 10 SECONDS!!!
        foreach($hours as $key=>$val) {
            $thetimer[$i]=microtime();
            $hours[$key]['sent']=Statistics::model()->countSentByDates(date("Y-m-d H:i:s", $val['start']), date("Y-m-d H:i:s", $val['end']));
            $hours[$key]['read']=Statistics::model()->countReadByDates(date("Y-m-d H:i:s", $val['start']), date("Y-m-d H:i:s", $val['end']));
            $hours[$key]['legend']=date("g", ($val['start'])). "-".date("ga", ($val['end']));
            $hours[$key]['linkused']=Statistics::model()->countLinkUsedByDates(date("Y-m-d H:i:s", $val['start']), date("Y-m-d H:i:s", $val['end']));
            $i++;
        }
        $hours=array_reverse($hours);

        //////////////////////////////////////////
        // CURRENT WEEK STATISTICS
        //////////////////////////////////////////
        
        //Iterate through the last 7 days (in the $days array)
        //  - always collect statistics for today
        //  - for all other days, check if the statistics live in the statistics table
        //    and, if they don't, collect statistics for that day, and store them there
        
        $statistics=array();
        $thetimer[10]=microtime();
        foreach($days as $day) {
            $datekey=date("Y-m-d", $day);
            if(date("Y-m-d") != $datekey) {
                //Check the statistics database for records
                $todaystats=Statistics::model()->find("`date` = '$datekey'");
                if($todaystats) {
                    $statistics[$datekey]['sent']=$todaystats->emailsSent;
                    $statistics[$datekey]['queued']=$todaystats->emailsQueued;
                    $statistics[$datekey]['read']=$todaystats->emailsRead;
                    $statistics[$datekey]['bounce']=$todaystats->emailBounces;
                    $statistics[$datekey]['linkused']=$todaystats->linksUsed;
                    $statistics[$datekey]['newsletters']=$todaystats->newslettersQueued;
                }
            }
            if(!isset($statistics[$datekey])) {
                $statistics[$datekey]['sent']=Statistics::model()->countSentByDate($day);
                $statistics[$datekey]['queued']=Statistics::model()->countQueuedByDate($day);
                $statistics[$datekey]['read']=Statistics::model()->countReadByDate($day);
                $statistics[$datekey]['bounce']=Statistics::model()->countBounceByDate($day);
                $statistics[$datekey]['linkused']=Statistics::model()->countLinkUsedByDate($day);
                $statistics[$datekey]['newsletters']=Statistics::model()->countNewslettersByDate($day);        
            }
            if(date("Y-m-d") != $datekey && isset($statistics[$datekey]) && !isset($todaystats)) {
                //It's not for today, and there's no record in the statistics table
                // so save the daily figures for future use
                $stats=new Statistics;
                $stats->date=$datekey;
                $stats->emailsSent=$statistics[$datekey]['sent'];
                $stats->emailsRead=$statistics[$datekey]['read'];
                $stats->emailBounces=$statistics[$datekey]['bounce'];
                $stats->linksUsed=$statistics[$datekey]['linkused'];
                $stats->newslettersQueued=$statistics[$datekey]['newsletters'];
                $stats->save();
            }
        }

        $statistics=array_reverse($statistics);
        
        $thetimer[11]=microtime();
        $thisweek=array("sent"=>0, "queued"=>0, "read"=>0, "linkused"=>0, "newsletters"=>0, "bounce"=>0);
        foreach($statistics as $stat) {
            $thisweek['sent']+=$stat['sent'];
            $thisweek['queued']+=$stat['queued'];
            $thisweek['read']+=$stat['read'];
            $thisweek['linkused']+=$stat['linkused'];
            $thisweek['newsletters']+=$stat['newsletters'];
            $thisweek['bounce']+=$stat['bounce'];         
        }
        
        
        $this->render('index', array(
            'statistics'=>$statistics,
            'thisweek'=>$thisweek,
            'userpending'=>$userpending,
            'usertoday'=>$usertoday,
            'userlast10'=>$userlast10,
            'topchitter'=>$topchitter,
            'topwchitter'=>$topwchitter,
            'topfchitter'=>$topfchitter,
            'hours'=>$hours,
            'readsummary'=>$readsummary,
            'summarysince'=>$starttime,
            'thetimer'=>$thetimer,
        ));
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				//use 'contact' view from views/mail
				$mail = new YiiMailer('contact', array('message' => $model->body, 'name' => $model->name, 'description' => 'Contact form'));

				//set properties
				$mail->setFrom($model->email, $model->name);
				$mail->setSubject($model->subject);
				$mail->setTo(Yii::app()->params['adminEmail']);
				//send
				if ($mail->send()) {
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				} else {
					Yii::app()->user->setFlash('error','Error while sending email: '.$mail->getError());
				}
				
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}
    
    public function actionAdmin() 
    {
        if(isset($_GET['action']) && $_GET['action'] == "togglequeueprocessing") {
            $basedir=Yii::app()->basePath;
            $dbfail=$basedir."/../tmp/dbfailure.ctk";

            if(!file_exists($dbfail)) {
                file_put_contents($dbfail, "User selected suspension of queue activity.");
            } else {
                rename($dbfail, $basedir."/../tmp/dbfailure.".date("Ymdhi").".txt");
            }
        }
        if(isset($_GET['action']) && $_GET['action'] == "clearqueuelock") {
            $basedir=Yii::app()->basePath;
            $dbqueue=$basedir."/../tmp/queuelock.txt";
            
            //Set the queuelock file time to right now, allowing the next queue process to run
            file_put_contents($dbqueue, time()-300);
        }
        $this->render('admin');    
    }
    
    public function actionLogs()
    {
        //Read the stats log
        $log_file = Yii::app()->dbConfig->getValue('stats_log_file') ? Yii::app()->dbConfig->getValue('stats_log_file') : '/var/www/chitkar/tmp/remoteStats.log';
        $statslog = file_get_contents($log_file, true);
        $statslog=nl2br($statslog);
        
        $log_file = Yii::app()->dbConfig->getValue('queue_log_file') ? Yii::app()->dbConfig->getValue('queue_log_file') : '/var/www/chitkar/tmp/queue.log';
        $queuelog = file_get_contents($log_file, true);
        $queuelog=nl2br($queuelog);
        
        $queuelock['started']=date("H:i:s, d M Y", filemtime('/var/www/html/chitkar/tmp/queuelock.txt'));
        $queuelock['until']=date("H:i:s, d M Y", file_get_contents('/var/www/html/chitkar/tmp/queuelock.txt')+60);
        $queuelock['expected']= date("H:i:s, d M Y", filemtime('/var/www/html/chitkar/tmp/queuelock.txt')+300);       
                        
        $this->render('logs', array(
            'statslog'=>$statslog,
            'queuelog'=>$queuelog,
            'queuelock'=>$queuelock,
        ));
    }
    
    public function actionTrimStatsLog($lines) {
        $log_file = Yii::app()->dbConfig->getValue('stats_log_file') ? Yii::app()->dbConfig->getValue('stats_log_file') : '/var/www/chitkar/tmp/remoteStats.log';
        $statslog = file_get_contents($log_file, true);
        $statslog = explode("\n", $statslog);
        if(!is_numeric($lines)) {
            if($lines=="half") $lines=(count($statslog))/2;
            if($lines=="quarter") $lines=(count($statslog))/4;
            if($lines=="all") $lines=count($statslog)-50;
        }
        $i=0;
        foreach($statslog as $line) {
            if($i > $lines)
                $newstatslog[]=$line;
            $i++;
        }
        $newstatslog=implode("\n", $newstatslog);
        $result=file_put_contents($log_file, $newstatslog);
        
        $this->redirect(array('site/logs'));
    }

    public function actionTrimQueueLog($lines) {
        $log_file = Yii::app()->dbConfig->getValue('queue_log_file') ? Yii::app()->dbConfig->getValue('queue_log_file') : '/var/www/chitkar/tmp/queue.log';
        $queuelog = file_get_contents($log_file, true);
        $queuelog = explode("\n", $queuelog);
        if(!is_numeric($lines)) {
            if($lines=="half") $lines=(count($queuelog))/2;
            if($lines=="quarter") $lines=(count($queuelog))/4;
            if($lines=="all") $lines=count($queuelog)-50;
        }
        $i=0;
        foreach($queuelog as $line) {
            if($i > $lines)
                $newqueuelog[]=$line;
            $i++;
        }
        $newqueuelog=implode("\n", $newqueuelog);
        $result=file_put_contents($log_file, $newqueuelog);
        
        $this->redirect(array('site/logs'));
    }
    
	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}