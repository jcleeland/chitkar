<?php

class OutgoingsController extends Controller
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
				'actions'=>array('index', 'view','processQueue'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','status', 'readlist', 'linklist', 'faillist'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
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
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

    public function actionReadList($id) {
        $model=Outgoings::model()->findAll(array(
                                            "condition"=>"newslettersId = $id", 
                                            "order"=>"`readTime` DESC",
                                            )
                                        );

        $this->render('readlist', array(
            'model'=>$model,
        ));
    }

    public function actionLinkList($id) {
        
        $summary=Yii::app()->db->createCommand()
                 ->select('distinct link, count(*) as total')
                 ->from('outgoings')
                 ->group('link')
                 ->order('count(*) DESC')
                 ->where('newslettersId = '.$id.' AND link != ""')
                 ->queryAll();
        //$summary="";
                 
        
        $model=Outgoings::model()->findAll(array(
                                            "condition"=>"newslettersId = $id AND link != ''", 
                                            "order"=>"`linkUsedTime` DESC",
                                            )
                                           );

        $this->render('linklist', array(
            'model'=>$model,
            'summary'=>$summary,
        ));
        
    }
    
    public function actionFailList($id) {
        $model=Outgoings::model()->findAll(array(
                                                "condition"=>"newslettersId = $id AND sendFailures > 0",
                                                "order"=>"`sendFailures` DESC",
                                                )
                                          );
        $this->render('faillist', array(
            'model'=>$model,
        ));
    }
    
    public function actionStatus($id) {
        
        $statistics=array();
        $basesql="SELECT COUNT(id) as total FROM outgoings WHERE newslettersId = ".$id;
        $statistics['total'] = Yii::app()->db->createCommand($basesql)->queryScalar();
        $sentsql=$basesql . " AND sent = 1";
        $statistics['sent'] = Yii::app()->db->createCommand($sentsql)->queryScalar();
        $statistics['percentsent'] = round(($statistics['sent']/$statistics['total'])*100);
        $readsql=$basesql." AND `read` = 1";
        $statistics['read'] = Yii::app()->db->createCommand($readsql)->queryScalar();
        $statistics['percentread'] = round(($statistics['read']/$statistics['total'])*100);
        $linksql=$basesql." AND `linkUsed` = 1";
        $statistics['links'] = Yii::app()->db->createCommand($linksql)->queryScalar();
        $statistics['percentlinked'] = round(($statistics['links']/$statistics['total'])*100);
        
        $queued=Outgoings::model()->findAll(array(
            "condition"=>"newslettersId=".$id, 
            "order" => "queueDate",
            "limit" => 1,
            )
        );
        
        $statistics['queued']='NA';
        foreach($queued as $qr) {
            $statistics['queued']=date("g:ia d/m/y", strtotime($qr->queueDate));
        }
                
        $mostrecent=Outgoings::model()->findAll(array(
            "condition"=>"newslettersId=".$id." AND sent=1", 
            "order" => "dateSent",
            "limit" => 1,
            )
        );
        
        $statistics['lastsent']='NA';
        foreach($mostrecent as $mr) {
            $statistics['lastsent']=date("g:ia d/m/y", strtotime($mr->dateSent));
        }
        
        
        $newsletter=Newsletters::model()->find("id=:newslettersId", array(":newslettersId"=>$id));
        $statistics['name']=$newsletter->title;
        $statistics['started']=date("g:ia d/m/y", strtotime($newsletter->sendDate));
        $this->render('status', array(
            'statistics'=>$statistics,
            'newsletter'=>$newsletter,        
        ));
    }
    
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Outgoings;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Outgoings']))
		{
			$model->attributes=$_POST['Outgoings'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Outgoings']))
		{
			$model->attributes=$_POST['Outgoings'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex($recipid='',$recipemail='',$newslettersid='',$reciplistid='')
	{
        $criteria=new CDbCriteria();
        //$criteria->order='t.id DESC';
        $newsletters=Newsletters::model()->findAll('queued=1');
		
        $criteria->with=array('newsletters');
        //die("-->".$recipid);
        
        if(isset($_GET['newsletterId'])) {
            //Filter by $_GET['newsletterId]
            $criteria->addSearchCondition('newslettersId', $_GET['newsletterId'], true, 'OR');
        }
        if(strlen($recipid)>0) {
            $criteria->addSearchCondition('recipientId', $recipid, true, 'AND');
        }
        if(strlen($recipemail)>0) {
            $criteria->addSearchCondition('email', $recipemail, true, 'AND');
        }
        if(strlen($newslettersid)>0) {
            $criteria->addSearchCondition('newslettersId', $newslettersid, true, 'AND');
        }
        if(strlen($reciplistid)>0) {
            $criteria->addSearchCondition('t.recipientListsId', $reciplistid, true, 'AND');
        }


        
        $dataProvider=new CActiveDataProvider('Outgoings', array(
            'criteria'=>$criteria,
            'sort' => array(
                'defaultOrder'=>'t.id DESC',
            ),
            'pagination'=>array(
                'pageSize'=>50,
                )           
            )
        );
        
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
            'newsletters'=>$newsletters,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Outgoings('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Outgoings']))
			$model->attributes=$_GET['Outgoings'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}
    
    public function actionProcessQueue() {
        
        $now=date("Y-m-d H:i:s");
        $jobs=Outgoings::model()->findAll('sendDate <:now AND sent != 1 ORDER BY id ASC', array(':now'=>$now));
        
        $fromemail='news@cpsuvic.org'; //TODO: Replace with database configuration
        $fromname="CPSU News Server";
                
        $mail = new YiiMailer();
        $mail->clearLayout();
        $mail->setFrom($fromemail, $fromname);
        $currentnewsletter=null;    
        foreach ($jobs as $job) {
            $newsletter=Newsletters::model()->find('id=:newslettersId', array(':newslettersId'=>$job->newslettersId));
            $mail->setSubject($newsletter->subject);
            $mail->setBody($newsletter->completed_html);
            $mail->setTo("jcleeland@cpsuvic.org");
            //$mail->setTo(array("jcleeland@cpsuvic.org"=>$job->email));
            //$mail->setTo($job->email);
            if($mail->send()) {
                $job->dateSent=$now;
                $job->sent = 1;
                $job->save();
            } 
        }
        die();
    }

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Outgoings the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Outgoings::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Outgoings $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='outgoings-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
