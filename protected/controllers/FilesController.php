<?php

class FilesController extends Controller
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
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','delete', 'admin', 'imagelist_json'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
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

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Files;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Files']))
		{
			$model->attributes=$_POST['Files'];
            //echo "<pre>"; print_r($_FILES); die();
            
            /** Insert the uploaded file into the database BLOB **/
            if(!empty($_FILES['Files']['tmp_name']['file']))
            {
                $file=CUploadedFile::getInstance($model, 'file');
                $fp=fopen($file->tempName, 'r');
                $content=fread($fp, filesize($file->tempName));
                fclose($fp);
                $model->file_name=$_FILES['Files']['name']['file'];
                $model->file_type=$_FILES['Files']['type']['file'];
                $model->file_size=$_FILES['Files']['size']['file'];
                $model->file = $content;
            }
            
			if($model->save())
            {
                //Upload the new file to the website
                $ftp_server=Yii::app()->dbConfig->getValue('ftp_server');
                $ftp_user_name=Yii::app()->dbConfig->getValue('ftp_username');
                $ftp_user_pass=Yii::app()->dbConfig->getValue('ftp_password');
                $ftp_file_remote=Yii::app()->dbConfig->getValue('ftp_read_file_location')."/images/".$model->file_name;
                //Connect to the ftp server
                $conn_id=ftp_connect($ftp_server);
                $login_result=ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
                if(!($conn_id) || !($login_result)) {
                    $errors = "FTP connection failed attempting to connect to $ftp_server for $ftp_user_name";    
                } else {
                    $download=ftp_put($conn_id, $ftp_file_remote, $file->tempName, FTP_BINARY);
                    $permission=ftp_chmod($conn_id, 0644, $ftp_file_remote);
                }                
                
                
                $this->redirect(array('view','id'=>$model->id));
            }
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

		if(isset($_POST['Files']))
		{
			$model->attributes=$_POST['Files'];
            /** Insert the uploaded file into the database BLOB **/
            
            if(!empty($_FILES['Files']['tmp_name']['file']))
            {
                //Catch if new file name is not the same as the old one
                if($_FILES['Files']['name']['file'] == $model->file_name)
                {
                    $file=CUploadedFile::getInstance($model, 'file');
                    $fp=fopen($file->tempName, 'r');
                    $content=fread($fp, filesize($file->tempName));
                    fclose($fp);
                    $model->file_size=$_FILES['Files']['size']['file'];
                    $model->file = $content;
                } else {
                    //FILE WITH DIFFERENT NAME ATTEMPTED TO BE UPLOADED
                }                
            }
			if($model->save())
            {
                //Delete the old file, upload the new one
                                
                $this->redirect(array('view','id'=>$model->id));
            }
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
		$current=$this->loadModel($id);
        $file_name=$current->file_name;
        
        $errors=null;
        //CHECK TO SEE IF THE FILE IS BEING USED IN CURRENT NEWSLETTERS
        if($filelink=FileLinks::model()->find('filesId='.$id)) {
            $errors="This file is currently being used in a non-archived newsletter and cannot be deleted";
        } else {
            //Delete the new file from the website
            $ftp_server=Yii::app()->dbConfig->getValue('ftp_server');
            $ftp_user_name=Yii::app()->dbConfig->getValue('ftp_username');
            $ftp_user_pass=Yii::app()->dbConfig->getValue('ftp_password');
            $ftp_file_remote=Yii::app()->dbConfig->getValue('ftp_read_file_location')."/images/".$file_name;
            //Connect to the ftp server
            $conn_id=ftp_connect($ftp_server);
            $login_result=ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
            if(!($conn_id) || !($login_result)) {
                $errors = "FTP connection failed attempting to connect to $ftp_server for $ftp_user_name";    
            } else {
                $download=ftp_delete($conn_id, $ftp_file_remote);
            }                
            
            // DELETE THE FILE FROM THE DATABASE
            if(!$errors)
                $this->loadModel($id)->delete();
        }
        
        if($errors)
            Yii::app()->user->setFlash('error', $errors);
		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex($string='')
	{

        $criteria=new CDbCriteria();
        if(strlen($string) > 0) 
            $criteria->addSearchCondition('description', $string, true, 'OR');
        $dataProvider=new CActiveDataProvider('Files', array('criteria'=>$criteria));
        $dataProvider->sort->defaultOrder='description ASC';
        $this->render('index',array(
            'dataProvider'=>$dataProvider,
        ));
                
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Files('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Files']))
			$model->attributes=$_GET['Files'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Files the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Files::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Files $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='files-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
    
    public function actionImagelist_json()
    {
        $model=Files::model()->findAll("file_type LIKE 'image/%'");
        $imageurl=Yii::app()->dbConfig->getValue('public_web_url')."images/";
        foreach($model as $item) {
            $data[]=array("image"=>$imageurl.$item->file_name,
                          "thumb"=>$imageurl.$item->file_name,
                          "folder"=>"Parent");   
        }
        header('Content-type: application/json');
        echo CJSON::encode($data);
        Yii::app()->end();
    }
}
