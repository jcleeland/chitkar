<?php

	//file uplaod enable for kcfinder
	$_SESSION['KCFINDER']['disabled'] = false; // enables the file browser in the admin
	$_SESSION['KCFINDER']['uploadURL'] = Yii::app()->baseUrl."/uploads/"; // URL for the uploads folder
	$_SESSION['KCFINDER']['uploadDir'] = Yii::app()->basePath."/../uploads/"; // path to the uploads folder

class TemplatesController extends Controller
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
			array('allow',   // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view','loadImage'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','contentpreview'),
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

	/*
	* for image display from blob
	*/
	public function actionloadImage($id)
    {
        $model=$this->loadModel($id);
        $this->renderPartial('image', 
                            array('model'=>$model
                                 )
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
    
    public function actionContentPreview($id)
    {
         $template=Templates::model()->findByPk($id);
         $contenttext="<h1>Unbelievably Brilliant IT People</h1><p><strong>Recent revelations prove that Chitkar developers are the most brilliant in the known world.</strong></p><p>The revelations were printed in the annual Google report on innovation and genius in unlikely places. This year's report was cited by hundreds of unknown people as being the most amazing ever.</p><p>You can meet Chitkar's lauded IT staff by simply visiting them at their workstations.</p>";
         $contenttext.="<h1>Amazing Software Development</h1><p><strong>A new software tool, Chitkar, is wowing the member-based organisation community with its powerful ease of use.</strong></p><p>Chitkar, which is the Nepalese word for 'Shout', was developed by Jason Cleeland and Ganesh Malla to help member based organisations improve their communications functionality. It integrates with pre-existing membership databases to allow direct mailing to portions of a membership.</p>";
         $content=str_replace("{CONTENT}", $contenttext, $template->html);
         $this->render('contentpreview', array(
            'content'=>$content,
         ));        
    }

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Templates;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Templates']))
		{
			$model->attributes=$_POST['Templates'];
			//
			if(!empty($_FILES['Templates']['tmp_name']['thumb_img']))
            {
                $file = CUploadedFile::getInstance($model,'thumb_img');
                $fp = fopen($file->tempName, 'r');
                $content = fread($fp, filesize($file->tempName));
                fclose($fp);
                $model->thumb_img = $content;
            }
 			/*
            $model->users = Yii::app()->users->id;
            if($model->save())
                $this->redirect(array('view','id'=>$model->id));
	        }
				
            $model->users = Yii::app()->users->id;
            if($model->save()) {
                $this->redirect(array('view','id'=>$model->id));
	        }
			*/

			if($model->save()) {
				$this->redirect(array('view','id'=>$model->id));
			}
        }
        
		$this->render('create',array(
			'model'=>$model,
			//
			'types'=>array("PDF File", "DOC File"),
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

		if(isset($_POST['Templates']))
		{
			$model->attributes=$_POST['Templates'];
			//
			if(!empty($_FILES['Templates']['tmp_name']['thumb_img']))
            {
                $file = CUploadedFile::getInstance($model,'thumb_img');
                $fp = fopen($file->tempName, 'r');
                $content = fread($fp, filesize($file->tempName));
                fclose($fp);
                $model->thumb_img = $content;
            }
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
			//
			'types'=>array("PDF File", "DOC File"),
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
	public function actionIndex()
	{
        $dataProvider=new CActiveDataProvider('Templates'); 
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
		$model=new Templates('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Templates']))
			$model->attributes=$_GET['Templates'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Templates the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Templates::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Templates $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='templates-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
