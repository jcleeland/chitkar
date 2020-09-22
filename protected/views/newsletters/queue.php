<?php
    if($newsletter->queued != 1 && Yii::app()->user->can_create) {
        $this->renderPartial('_queueForm', array(
                                    'model'=>$model,
                                    'newsletter'=>$newsletter, 
                                    'template'=>$template, 
                                    'content'=>$content,
                                    )
                            );
    } else {
        echo "You are not authorised to queue newsletters";
    }
    
    
    Yii::app()->end(); 
?>
