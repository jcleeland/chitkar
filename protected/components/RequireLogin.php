<?php
class RequireLogin extends CBehavior
{
    public function attach($owner)
    {
        $owner->attachEventHandler('onBeginRequest', array($this, 'handleBeginRequest'));
    }
    
    public function handleBeginRequest($event)
    {
        //Final array is list of pages that DON'T require a login
        if (Yii::app()->user->isGuest && 
            isset($_GET['r']) && 
            !in_array($_GET['r'],array('site/login' , 
                                       'site/index'
                                       ))
            )
        {
            Yii::app()->user->loginRequired();
        }
    }    
}
?>
