<?php

class NewslettersController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array(),
				'users'=>array(''),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create', 'update', 'preview', 'getsql', 'contentpreview', 'index', 'view', 'archive', 'nudge'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete','queue','unqueue'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{   
        $model=Newsletters::model()->with('users', 'recipientLists', 'templates')->findByPk($id);
        $template=$model->templates;



        $content=str_replace("{CONTENT}", $model->content, $template->html);

        $statistics=array("total"=>"NA", "sent"=>"NA", "read"=>"NA", "percentread"=>"NA");
        if($model->queued == 1) {
            $basesql="SELECT COUNT(id) as total FROM outgoings WHERE newslettersId = ".$id;
            $statistics['total'] = Yii::app()->db->createCommand($basesql)->queryScalar();
            $sentsql=$basesql . " AND sent = 1";
            $statistics['sent'] = Yii::app()->db->createCommand($sentsql)->queryScalar();
            if($statistics['total']==0) $statistics['total']=1;
            $statistics['percentsent'] = round(($statistics['sent']/$statistics['total'])*100);
            $readsql=$basesql." AND `read` = 1";
            $statistics['read'] = Yii::app()->db->createCommand($readsql)->queryScalar();
            $statistics['percentread'] = round(($statistics['read']/$statistics['total'])*100);
            $linksql=$basesql . " AND linkUsed = 1";
            $statistics['links'] = Yii::app()->db->createCommand($linksql)->queryScalar();
            $statistics['percentlinked'] = round(($statistics['links']/$statistics['total'])*100);
            $readsql=$basesql." AND `read` = 1";
            $statistics['read'] = Yii::app()->db->createCommand($readsql)->queryScalar();
            $statistics['percentread'] = round(($statistics['read']/$statistics['total'])*100);
            $statistics['average_read_gap']=Statistics::model()->averageReadGapByNewsletter($model->id);
            $quartiles=Statistics::model()->quartilesReadGapByNewsletter($model->id);
            $statistics['firstq_read_gap']=$quartiles['first'];
            $statistics['median_read_gap']=$quartiles['second'];
            $statistics['thirdq_read_gap']=$quartiles['third'];
            $statistics['firstq_read_gap_count']=$quartiles['firstcount'];
            $statistics['median_read_gap_count']=$quartiles['secondcount'];
            $statistics['thirdq_read_gap_count']=$quartiles['thirdcount'];
            $failquery="SELECT count(id) as total FROM outgoings WHERE newslettersId=".$id." AND sendFailures > 0 and sent = 0 ORDER BY queueDate";
            $statistics['failurereport']=Yii::app()->db->createCommand($failquery)->queryScalar();
        }
		$this->render('view',array(
			'model'=>$this->loadModel($id),
            'content'=>$content,
            'statistics'=>$statistics,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Newsletters;
        $externaldb=new ExternalDb;
        
        $fieldnames=$externaldb->fields;
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Newsletters']))
		{
            $model->attributes=$_POST['Newsletters'];
            $model->content = preg_replace('/[\x00-\x1F\x7F\xE2\x80\x8B]/u', '', $model->content);
            $model->content = str_replace("\xE2\x80\x8B", '', $model->content);            
            
            if($model->save())
            {
                //Find any images inserted from the files database
                preg_match_all('#(\<\s*img [^\>]*\>)#im', $model->content.".......", $matches, PREG_SET_ORDER);
                $img=array();
                foreach($matches as $img_tag) 
                {
                    preg_match_all('/(src)=("[^"]*")/i',(string)$img_tag[0], $img[]);
                }
                $filenames=array();
                foreach($img as $image) {
                    $filenames[]=substr($image[2][0], strlen(Yii::app()->dbConfig->getValue('public_web_url')."images/ "), -1 );
                }
                $filenames[]="popop.jpg";
                //$filename=substr()
                foreach($filenames as $filename) 
                {
                     //Search the files model for a matching filename
                     if($filesinfo=Files::model()->find("file_name='".$filename."'"))
                     {     
                         //Update the filesused table to indicate it is being used
                         $newsletterid=$model->id;
                         $fileLink=new FileLinks;
                         $fileLink->filesId=$filesinfo->id;
                         $fileLink->newslettersId=$newsletterid;
                         $fileLink->save();
                     }
                }
                $this->redirect(array('index','id'=>$model->id));
            }
		}

        if(isset($_GET['copyid'])) {
            $model=$this->loadModel($_GET['copyid']);
            $model->id=null;
            $model->usersId=null;
            $model->sendDate=null;
        }
        
		$this->render('create',array(
            'fields'=>$fieldnames,
			'model'=>$model,
            'library'=>$externaldb->library,
            'starters'=>$externaldb->starters,
		));
	}

    public function actionNudge($id) {
        $originalNewsletter=Newsletters::model()->findByPk($id);
        if(!$originalNewsletter) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        //Fetch the non-readers from the outgoings table
        $nonReaders=Outgoings::model()->findAll('newslettersId=:newslettersId AND `read`=0', array(':newslettersId'=>$id));

        if(empty($nonReaders)) {
            Yii::app()->user->setFlash('info', 'All recipients have read this newsletter - so we cannot create a nudge newsletter.');
            //echo "No non-readers found."; die();
            $this->redirect(array('view', 'id'=>$id));
            return;
        }

        //Clone the original newsletter for follow-up
        $followUpNewsletter = new Newsletters();
        $followUpNewsletter->attributes = $originalNewsletter->attributes;
        $followUpNewsletter->title = 'NUDGE - ' . $originalNewsletter->title;
        $followUpNewsletter->subject = 'Re: Did you miss this? - ' . $originalNewsletter->subject;
        $followUpNewsletter->sendDate = new CDbExpression('NOW()');
        $followUpNewsletter->queued = 0;  // Not queued yet
        $followUpNewsletter->completed = 0;  // Not completed yet
        $followUpNewsletter->recipientValues = 'nudge:' . $originalNewsletter->id;
        $followUpNewsletter->recipientCount=0;
        $followUpNewsletter->created = new CDbExpression('NOW()');
        $followUpNewsletter->modified = new CDbExpression('NOW()');
        $followUpNewsletter->usersId=Yii::app()->user->id;
        $followUpNewsletter->recipientListsId=null;



        $followUpNewsletter->save();
        $newNewsletterId=$followUpNewsletter->id;

        //Now load up the edit newsletter page using the new newsletter id
        $this->redirect(array('update', 'id'=>$newNewsletterId));


        
        //Stop everything... this isn't complete yet
        /* Yii::app()->user->setFlash('info', '❗The "Nudge slackers" feature is not yet complete. Come back when Jason has finished it. Give him chocolate if you want it done faster.');
        $this->redirect(array('view', 'id'=>$id));
        return;
        
        // Queue the follow-up for non-readers
        foreach ($nonReaders as $recipient) {
            $outgoing = new Outgoings();
            $outgoing->attributes = $recipient->attributes;
            $outgoing->newslettersId = $followUpNewsletter->id;
            $outgoing->queueDate = new CDbExpression('NOW()');
            $outgoing->save();
        }

        Yii::app()->user->setFlash('success', 'Nudge newsletter has been created and queued.');
        echo $followUpNewsletter->id;
        die();
        $this->redirect(array('view', 'id' => $followUpNewsletter->id)); */


    }

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);
        $externaldb=new ExternalDb;

        $fieldnames=$externaldb->fields;
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Newsletters']))
		{
			$model->attributes=$_POST['Newsletters'];
            //Removal of some of the more annoying Microsoft Word characters
            $model->content = preg_replace('/[\x00-\x1F\x7F\xE2\x80\x8B]/u', '', $model->content);
            $model->content = str_replace("\xE2\x80\x8B", '', $model->content);
            
			if($model->save())
                //Delete file links before re-adding them
                FileLinks::model()->deleteAll('newslettersId = ?', array($id));
                //Find any images inserted from the files database
                preg_match_all('#(\<\s*img [^\>]*\>)#im', $model->content.".......", $matches, PREG_SET_ORDER);
                $img=array();
                foreach($matches as $img_tag) 
                {
                    preg_match_all('/(src)=("[^"]*")/i',(string)$img_tag[0], $img[]);
                }
                $filenames=array();
                foreach($img as $image) {
                    $filenames[]=substr($image[2][0], strlen(Yii::app()->dbConfig->getValue('public_web_url')."images/ "), -1 );
                }
                //$filename=substr()
                foreach($filenames as $filename) 
                {
                     //Search the files model for a matching filename
                     if($filesinfo=Files::model()->find("file_name='".$filename."'"))
                     {     
                         //Update the filesused table to indicate it is being used
                         $newsletterid=$model->id;
                         $fileLink=new FileLinks;
                         $fileLink->filesId=$filesinfo->id;
                         $fileLink->newslettersId=$newsletterid;
                         $fileLink->save();
                     }
                }

            $this->redirect(array('index'));


        }

		$this->render('update',array(
			'model'=>$model,
            'fields'=>$fieldnames,
            'library'=>$externaldb->library,
            'starters'=>$externaldb->starters,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
        //Delete all the related outgoings records
        Outgoings::model()->deleteAll('newslettersId = ?', array($id));
		FileLinks::model()->deleteAll('newslettersId = ?', array($id));
        $this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex($string='',$userId=null)
	{

        $criteria=new CDbCriteria();
        if($userId)
            $criteria->addSearchCondition('usersId', $userId, true, 'AND');
        
        $criteria->addSearchCondition('queued', '1', true, 'AND', 'NOT LIKE');
        $criteria->addSearchCondition('completed', '1', true, 'AND', 'NOT LIKE');
        
        //$criteria->condition = 'queued <> 1 AND completed <> 1';
        $criteria->with=array('users', 'templates', 'recipientLists');
        $dataProvider=new CActiveDataProvider('Newsletters',
            array('criteria'=>$criteria)
        );
        $dataProvider->sort->defaultOrder='senddate ASC';
        
        $criteria=new CDbCriteria();
        if($userId)
            $criteria->addSearchCondition('usersId', $userId, true, 'AND');
        
        $criteria->addSearchCondition('queued', '1', true, 'AND', 'LIKE');
        $criteria->addSearchCondition('completed', '1', true, 'AND', 'NOT LIKE');
        $criteria->with=array('users', 'templates', 'recipientLists');
        
        $queuedDataProvider=new CActiveDataProvider('Newsletters',
                array('criteria'=>$criteria)
            );
        $queuedDataProvider->sort->defaultOrder='senddate ASC';
        
        
        //COMPLETED NEWSLETTERS
        $criteria=new CDbCriteria();
        if(strlen($string) > 0) 
            $criteria->addSearchCondition('title', $string, true, 'AND');
        
        if($userId)
            $criteria->addSearchCondition('usersId', $userId, true, 'AND');
        
        $criteria->addSearchCondition('queued', '1');
        $criteria->addSearchCondition('completed', '1');
        $criteria->addSearchCondition('archive', '0'); //Don't show archived newsletters  
        $criteria->with = array('users', 'templates', 'recipientLists');

        $recentDataProvider=new CActiveDataProvider('Newsletters',
            array('criteria'=>$criteria)
        );
        if(isset($_GET['completed']) && $_GET['completed']=="sent") {
            $recentDataProvider->sort->defaultOrder='senddate ASC';
        } else {
            $recentDataProvider->sort->defaultOrder='senddate DESC';
        }
        
        //ARCHIVED NEWSLETTERS
        $criteria=new CDbCriteria();
        if(strlen($string) > 0) 
            $criteria->addSearchCondition('title', $string, true, 'AND');
        
        if($userId)
            $criteria->addSearchCondition('usersId', $userId, true, 'AND');

        $criteria->addSearchCondition('queued', '1');
        $criteria->addSearchCondition('completed', '1');
        $criteria->addSearchCondition('archive', '1'); //Don't show archived newsletters  
        $criteria->with = array('users', 'templates', 'recipientLists', 'archives');

        $archivedDataProvider=new CActiveDataProvider('Newsletters',
            array('criteria'=>$criteria)
        );
        $archivedDataProvider->sort->defaultOrder='senddate DESC';
        
        
        $userdetails=$userId ? Users::model()->find('id='.$userId) : null;
        if($userdetails) {$user=$userdetails->firstname." ".$userdetails->lastname;} else {$user="";}
        
        $this->render('index',array(
			'dataProvider'=>$dataProvider,
            'queuedDataProvider'=>$queuedDataProvider,
            'recentDataProvider'=>$recentDataProvider,
            'archivedDataProvider'=>$archivedDataProvider,
            'user'=>$user,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
        $oldmodel=new CActiveDataProvider('Newsletters',
            array(
                'criteria'=>array(
                    //'condition'=>'queued <> 1',    
                    'with'=>array('users', 'templates', 'recipientLists'),    
                )
            ));
		$model=new Newsletters('search', 
                                   array(
                                    'with'=>array('users', 'templates', 'recipientLists'),
                                   )
                               );
		//$model->unsetAttributes();  // clear any default values
        if(isset($_GET['Newsletters'])) {
            $model->attributes=$_GET['Newsletters'];
        }
        //echo "<pre>"; print_r($_GET); die();
        $this->render('admin',array(
			'model'=>$model,
		));
	}
    
    /**
    * Displays a preview of a newsletter inside a template
    */
    public function actionPreview($id) 
    {
         $newsletter=Newsletters::model()->findByPk($id);
         $template=$newsletter->templates;
         $content=str_replace("{CONTENT}", $newsletter->content, $template->html);
         $subject=$newsletter->subject;
         $this->render('preview', array(
            'content'=>$content,
            'subject'=>$subject,
            'id'=>$id,
         ));
    }

    public function actionArchive($id) 
    {
        if(strpos($id, "|") > -1) {
            $idlist=explode("|", $id);
            $last=count($idlist)-1;
            unset($idlist[$last]);
            
        } else {
            $idlist[]=$id;
        }
        
        $content=array();
        
        foreach($idlist as $id) {
            
            $newsletter=Newsletters::model()->findByPk($id);
            $totalSent=Outgoings::model()->count("sent = 1 AND newslettersId = :newslettersId", array(':newslettersId'=>$id));
            $totalRead=Outgoings::model()->count("`read` = 1 AND newslettersId = :newslettersId", array(':newslettersId'=>$id));
            $totalLinks=Outgoings::model()->count("linkUsed= 1 AND newslettersId = :newslettersId", array(':newslettersId'=>$id));
            $dateArchived=date("Y-m-d", time());
            $usercriteria=new CDbCriteria();
            $usercriteria->select="email, recipientId";
            $usercriteria->condition="newslettersId = ".$id;
            $usercriteria->order="recipientId"; 
            $outgoings=Outgoings::model()->findAll($usercriteria);
            $emails=array();
            $recipientIds=array();
            foreach($outgoings as $row) {
                $emails[]=$row->email;
                $recipientIds[]=$row->recipientId;
            } 
            $emailList=implode("|", $emails);
            $recipientList=implode("|",$recipientIds);
            
            
            //Write the statistics to the archive table
            $insert=new Archives;
            $insert->newslettersId=$id;
            $insert->totalSent=$totalSent;
            $insert->totalRead=$totalRead;
            $insert->totalLinks=$totalLinks;
            $insert->dateArchived=$dateArchived;
            $insert->recipientEmails=$emailList;
            $insert->recipientIds=$recipientList;
            $insert->save();
            
            $newsletter->archive=1;
            $newsletter->update(array("archive"));
            
            Outgoings::model()->deleteAll("newslettersId = ".$id);
            
            $content[]=array("id"=>$id, "title"=>$newsletter->title, "sent"=>$totalSent, "read"=>$totalRead, "links"=>$totalLinks);
                        
        }

        
        //echo "<pre>"; print_r($output); echo "</pre>";
        $this->render('archive', array('content'=>$content));
    }
    
    public function actionContentPreview($id) 
    {
         $newsletter=Newsletters::model()->findByPk($id);
         $template=$newsletter->templates;
         $content=!empty($newsletter->completed_html) ? $newsletter->completed_html : str_replace("{CONTENT}", $newsletter->content, $template->html);
         $this->render('contentpreview', array(
            'content'=>$content,
         ));
    }
        
    /**
    * Returns the SQL from a newsletter
    */
    public function actionGetSql($id)
    {
        $newsletter=Newsletters::model()->findByPk($id);
        $sql=$newsletter->recipientSql;
        $this->render('getsql', array(
            'sql'=>$sql,
        ));
    }
    
    /**
     * Unqueues an email for delivery - only if it hasn't already started sending out yet
     */
    public function actionUnqueue($id) {
        $model=$this->loadModel($id);
        $condition = 'newslettersId=:newslettersId AND sent=:sent';
        $params = array(':newslettersId' => $id, ':sent' => 0);
        //1: Delete all the items in the outgoings table
        Outgoings::model()->deleteAll($condition, $params);

        //2: Reset the newsletter table so that this newsletter is unqueued
        $model->queued=0;
        $model->completed_html="";
        if($model->save())
            $this->redirect(array('index'));



    }
    
    /**
    * Queues an email for delivery, iterates it to the outgoings table
    * locks it from editing
    */
    public function actionQueue($id) 
    {
        ini_set('max_execution_time', '90'); //Set the maximum execution time to 90 seconds just in case
        $model=$this->loadModel($id);
        $newsletter=Newsletters::model()->findByPk($id);
        //$sql=$newsletter->recipientSql;
        $template=$newsletter->templates;
        //Find out if the $newsletter->recipientValues starts with "nudge:"
        $nudge=false;
        if(substr($newsletter->recipientValues, 0, 6) == "nudge:") {
            $nudge=true;
            $originalNewsletterId=substr($newsletter->recipientValues, 6);
            //Gather the non-readers from the original newsletter
            $nudgees=Outgoings::model()->findAll('newslettersId=:newslettersId AND (`read`=0 AND `linkUsed`=0)' , array(':newslettersId'=>$originalNewsletterId));
            $nudgeedata=array();
            
            foreach($nudgees as $nudgee) {
                $thisdata=json_decode($nudgee->data);

                // Initialize the array with required fields
                $nudgeeEntry = array(
                    'member' => $thisdata->member,
                    'pref_name' => $thisdata->pref_name,
                    'surname' => $thisdata->surname,
                    'email' => $thisdata->email,
                );
                // Add 'department' only if it exists
                if (isset($thisdata->department)) {
                    $nudgeeEntry['department'] = $thisdata->department;
                }        
                $nudgeedata[]=$nudgeeEntry; 
            }
        }
        //$newsletterhtml=Globals::linkify_links($newsletter->content);
        $newsletterhtml=$newsletter->content;
        if($newsletter->trackReads == 1) {
            $publicweburl=Yii::app()->dbConfig->getValue('public_web_url');
            $newsletterhtml .= "<img src='".$publicweburl."image.php?imgurl=chitkar.gif&nid=".$newsletter->id."&rid={RID}' width='1' height='1' />";
        }
        if($newsletter->trackLinks == 1) {
            $publicweburl = Yii::app()->dbConfig->getValue('public_web_url');

            // Exclude Microsoft Teams meeting links from being rewritten
            $newsletterhtml = preg_replace_callback(
                "/<a([^>]+)href=(\'|\")https?\:\/\/([a-zA-Z0-9\-\.]+\.[a-z]{2,5}(\/[^\'|\"]*)?)(\'|\")/i",
                function ($matches) use ($publicweburl, $newsletter) {
                    $url = $matches[3];

                    // List of patterns to exclude
                    $excludedPatterns = [
                        'teams.microsoft.com',
                        'teams.microsoft.com/dl/launcher/launcher.html',
                        'teams.microsoft.com/l/meetup-join/',
                        'teams.live.com/meet/',
                        'outlook.office365.com/owa/'
                    ];

                    foreach ($excludedPatterns as $pattern) {
                        if (strpos($url, $pattern) !== false) {
                            return $matches[0]; // Return the original link unchanged
                        }
                    }

                    // Rewrite the URL for tracking
                    return "<a{$matches[1]}href=\"{$publicweburl}links.php?URL=$url&nid={$newsletter->id}&rid={RID}\"";
                },
                $newsletterhtml
            );
            //$newsletterhtml = preg_replace("/<a([^>]+)href=(\'|\")https?\:\/\/([a-zA-Z0-9\-\.]+\.[a-z]{2,5}(\/[^\'|\"]*)?)(\'|\")/i", "<a$1href=\"".$publicweburl."links.php?URL=$3&nid=".$newsletter->id."&rid={RID}\"", $newsletterhtml);
            
       
        }
        $content=str_replace("{CONTENT}", $newsletterhtml, $template->html);
        $message="";
        if(isset($_POST['Newsletters']))
        {
            
            //Check if this is a nudge newsletter
            if($nudge) {

                //echo "<pre>"; print_r($model); echo "</pre>";
                $model->attributes=$_POST['Newsletters'];
                //echo "<pre>"; print_r($model); echo "</pre>"; die();
                $externalDb=new ExternalDb;
                $data = $nudgeedata;
                $count=count($data);
                $model->recipientCount=$count;

            } else {
                $model->attributes=$_POST['Newsletters'];
                $externalDb=new ExternalDb;
                //Iterate into $outgoings
                $return = $externalDb->execute($model->recipientSql);
                $dataerror = $return['error'];
                $data = $return['data'];
                $count=count($data);
                $model->recipientCount=$count;
            }

            //If the count of recipients is zero, then crash out and alert the user.
            if($count==0) {
                Yii::log("The definition/SQL for this newsletter returns zero recipients. Abortiong process - edit this newsletter so that it goes to someone!", 'error');
                
                // Set a flash message to notify the user
                Yii::app()->user->setFlash('error', "The definition/SQL for the newsletter you just tried to queue returns zero recipients.<br /><br />It has NOT been queued.<br /><br />Please edit this newsletter to ensure it reaches someone.");
    
                // Redirect to a standard page, e.g., the dashboard or the newsletter list
                $this->redirect(array('newsletters/index&id='.$id));
                
                // Prevent further execution to ensure the redirect happens
                Yii::app()->end();
            }

            // Check if any entries with the newslettersId already exist in the Outgoings table
            $existingEntries = Outgoings::model()->find('newslettersId=:newslettersId', array(
                ':newslettersId' => $id,
            ));

            if ($existingEntries !== null) {
                // Entries already exist, abort the process
                Yii::log("Entries with newslettersId {$id} already exist in the Outgoings table. Aborting process - contact your system administrator.", 'error');
                // Set a flash message to notify the user
                Yii::app()->user->setFlash('error', "Entries with newslettersId {$id} already exist in the Outgoings table. Aborting process - contact your system administrator.<br /><br />The newsletter has NOT been queued.");
    
                // Redirect to a standard page, e.g., the dashboard or the newsletter list
                $this->redirect(array('newsletters/index&id='.$id));
                
                // Prevent further execution to ensure the redirect happens
                Yii::app()->end();
                //return;
            }
            
            /* Make sure there are no duplicates */
            $uniques=array();
            $newdata=array();
            $duplicatecount=0;
            foreach($data as $recipient) {
                $uniqueval=$id.$recipient['member'].$recipient['email'];
                if(!in_array($uniqueval, $uniques)) {
                    array_push($uniques, $uniqueval);
                    array_push($newdata, $recipient);
                } else {
                    $duplicatecount++;
                }                
            }

            
            //Now $newdata should only have the unique entries from $data
            foreach($newdata as $recipient) {
                if($recipient['email'] != "") { //Don't add any entries that do not meet basic email validation
                    //Prepare the data
                    $storedata=json_encode($recipient);

                    
                    $outgoings=new Outgoings();
                    $outgoings->newslettersId=$id;
                    $outgoings->recipientListsId=$model->recipientListsId;
                    $outgoings->recipientId=isset($recipient['member']) ? $recipient['member'] : '';
                    $outgoings->email=$recipient['email'];
                    $outgoings->sendDate=$model->sendDate;
                    $outgoings->queueDate=date("Y-m-d H:i:s");
                    $outgoings->sent=0;
                    $outgoings->bounce=0;
                    $outgoings->bounceText="";
                    $outgoings->read=0;
                    $outgoings->linkUsed=0;
                    $outgoings->data=$storedata;
                    $outgoings->insert();
                }

            }
          

            if($model->save())
                $this->redirect(array('index'));
            
        }
                 
        $this->render('queue', array(
             'model'=>$model,
             'message'=>$message,
             'newsletter'=>$newsletter,
             'template'=>$template,
             'content'=>$content,
             
        ));    
    }

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Newsletters the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Newsletters::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}


	/**
	 * Performs the AJAX validation.
	 * @param Newsletters $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='newsletters-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
