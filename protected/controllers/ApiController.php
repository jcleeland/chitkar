<?php
class ApiController extends Controller
{
    public function filters()
    {
        return array();
    }    
    public function actionNewsletterList()
    {
        $this->checkApiKey();
        $output=array();
        $output['request']=$_GET;

        $sql="SELECT id, recipientListsId, templatesId, title, subject, completed_html, sendDate, recipientCount\n";
        $sql.="FROM newsletters\n";
        $sql.="WHERE completed=1\n";
        if(isset($_GET['startdate']) && !empty($_GET['startdate'])) {
            $sql.="AND sendDate >= '".$_GET['startdate']."'\n";
            $sql.="AND sendDate <= '".$_GET['enddate']."'\n";    
        }
        $sql.="ORDER BY sendDate\n";
        if(!isset($_GET['startdate']) || empty($_GET['startdate'])) {
            $sql.=" DESC LIMIT 5";
        }
        $output['sql']=$sql;
        $dbCommand = Yii::app()->db->createCommand($sql);
        $readsummary = $dbCommand->queryAll();        

                
        $output['newsletters']=$readsummary;
        header('Content-Type: application/json; charset=UTF-8');
        $this->layout = false;
        echo json_encode($output);
        Yii::app()->end();
    }
    
    public function actionNewsletter()
    {
        $this->checkApiKey(); 
        $output=array();
        $output['request']=$_GET;
        $sql="SELECT id, recipientListsId, templatesId, title, subject, completed_html, sendDate, recipientCount\n";
        $sql.="FROM newsletters\n";
        $sql.="WHERE id=".$_GET['bulletinid']."\n";  
        $output['sql']=$sql;
        $dbCommand = Yii::app()->db->createCommand($sql);
        $readsummary = $dbCommand->queryAll();        

                
        $output['newsletter']=$readsummary;
        header('Content-Type: application/json; charset=UTF-8');
        $this->layout = false;
        echo json_encode($output);
        Yii::app()->end();

    }
    
    public function actionStatistics()
    {
        $this->checkApiKey();
        $output=array();
        $output['request']=$_GET;
        
        header('Content-Type: application/json; charset=UTF-8');
        $this->layout = false;        
        echo json_encode($output);    
        Yii::app()->end();    
    }
    
    protected function checkApiKey()
    {
        $headers = apache_request_headers();
        if (!isset($headers['Api-Key']) || $headers['Api-Key'] != 'incre#dhask%009xdyyYYY') {
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(array('error' => 'Invalid API key.'));
            Yii::app()->end();
        }
    }    
}  
?>
