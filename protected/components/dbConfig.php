<?php
class dbConfig extends CApplicationComponent
{
    function getValue($key)
    {
        $model = Settings::model()->findByAttributes(array('setting_name'=>$key));
        return $model->setting_value;    
    }    
    
}
?>
