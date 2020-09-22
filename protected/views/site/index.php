<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<h1>Welcome to <i><?php echo CHtml::encode(Yii::app()->name); ?></i> Beta</h1>

<p><i>Chitkar is a bulk emailing tool designed for member organisations.</i></p>

<?php
    
if(Yii::app()->user->isGuest) {
?>
<p>
You should <?php echo CHtml::link('login', array('site/login'))?>.   
</p> 
<?php
} else {
?>
<p>
You are logged in as <?php echo Yii::app()->user->firstname; ?> <?php echo Yii::app()->user->lastname; ?>.
</p>
<?php
}
?>

<div style='background-color: silver; font-size: 8pt; padding: 5px'>
<p>You may change the content of this page by modifying the following two files:</p>
<ul>
	<li>View file: <code><?php echo __FILE__; ?></code></li>
	<li>Layout file: <code><?php echo $this->getLayoutFile('main'); ?></code></li>
</ul>
<p>For more details on how to further develop this application, please read
the <a href="http://www.yiiframework.com/doc/">documentation</a>.
Feel free to ask in the <a href="http://www.yiiframework.com/forum/">forum</a>,
should you have any questions.</p>
</div>


