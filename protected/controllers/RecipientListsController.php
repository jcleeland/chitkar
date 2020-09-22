<?php

class RecipientListsController extends Controller
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
				'actions'=>array('index','view'),
				'roles'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','JsonOutput'),
				'roles'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'roles'=>array('admin'),
			),
			array('deny',  // deny all users
				'roles'=>array('*'),
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

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new RecipientLists;
        
        $externaldb=new ExternalDb;
        
        $fieldnames=$externaldb->fields;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['RecipientLists']))
		{
			$model->attributes=$_POST['RecipientLists'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}
        $this->render('create',array(
			'model'=>$model,
            'fields'=>$fieldnames,
            'library'=>$externaldb->library,
            'starters'=>$externaldb->starters,
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
        $externaldb=new ExternalDb;
        
        $fieldnames=$externaldb->fields;
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['RecipientLists']))
		{
			$model->attributes=$_POST['RecipientLists'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
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
		//Remove references to this list from the newsletters table
        $newsletters=Newsletters::model()->findAll("recipientListsId = $id");
        foreach($newsletters as $job) {
            $job->recipientListsId='';
            $job->save();
        }
        
        $this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex($string='', $library='oms')
	{
		$criteria=new CDbCriteria();
        if(strlen($string) > 0) 
            $criteria->addSearchCondition('name', $string, true, 'OR');
        if(strlen($library) > 0)
            $criteria->addSearchCondition('library', $library, true, 'AND');
        $dataProvider=new CActiveDataProvider('RecipientLists', array('criteria'=>$criteria));
        $dataProvider->sort->defaultOrder='name ASC';
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new RecipientLists('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['RecipientLists']))
			$model->attributes=$_GET['RecipientLists'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}
    
    /**
    * This function delivers the SQL & values
    * fields for a recipient list ID in
    * json format
    * 
    */
    public function actionJsonOutput($id) 
    {
        $model=$this->loadModel($id);
        $output=array("sql"=>$model->sql, "values"=>$model->values);
        $this->render('json', array(
            'output'=>$output,
            )
        );
    }

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return RecipientLists the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=RecipientLists::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param RecipientLists $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='recipient-lists-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
