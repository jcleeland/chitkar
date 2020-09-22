<?php
  //$this->layout=false;
  header('Content-type: application/json');
  echo json_encode($dataerror);
  Yii::app()->end();
?>
