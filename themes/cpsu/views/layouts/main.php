<?php /* @var $this Controller */ 
$documentroot="/var/www/html/chitkar";
$webroot="https://live02.cpsuvic.org:4445";
$cssCoreUrl = Yii::app()->clientScript->getCoreScriptUrl();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <link rel="icon" type="image/x-icon" href="<?php echo Yii::app()->theme->baseUrl;?>/images/favicon.ico" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
    <?php Yii::app()->clientScript->registerCoreScript('jquery'); ?>
    <?php Yii::app()->clientScript->registerCoreScript('jquery.ui'); ?>
    <?php Yii::app()->clientScript->registerCssFile($cssCoreUrl.'/jui/css/base/jquery-ui.css'); ?>
    
    <!-- global-nav -->
	<!--<link rel="stylesheet" href="<?php echo "$webroot"?>/global/css/main-global.css" type="text/css" media="screen" />-->

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl;?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl;?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl;?>/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl;?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl;?>/css/form.css" />

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>
<?php //include("$documentroot/global/nav-global.php"); ?>

<div class="container" id="page">

	<div id="header">
		<div id="logo">
        	<img src="<?php echo Yii::app()->theme->baseUrl;?>/images/logo.png" />
			<?php echo CHtml::encode(Yii::app()->name); ?></div>
        <div id="login" style='text-align: right'>
            <?php if(Yii::app()->user->isGuest) {
                echo CHtml::link('Login', array('site/login'));
            } else {
                echo "Logged in as ".Yii::app()->user->firstname . " ". Yii::app()->user->lastname." | ";
                echo CHtml::link('Logout', array('site/logout'));    
            }
            ?>
            <br /><span id='servertime' style='cursor: pointer' title="Chitkar Server`s Default Timezone is <?php echo date_default_timezone_get(); ?>"><? print date("F d, Y H:i:s", time())?></span>
            <script type='text/javascript'>
                //Depending on whether your page supports SSI (.shtml) or PHP (.php), UNCOMMENT the line below your page supports and COMMENT the one it does not:
                //Default is that SSI method is uncommented, and PHP is commented:

                //var currenttime = '<!--#config timefmt="%B %d, %Y %H:%M:%S"--><!--#echo var="DATE_LOCAL" -->' //SSI method of getting server date
                var currenttime = '<?php print date("F d, Y H:i:s", time())?>' //PHP method of getting server date

                ///////////Stop editting here/////////////////////////////////

                var montharray=new Array("January","February","March","April","May","June","July","August","September","October","November","December")
                var serverdate=new Date(currenttime)

                function padlength(what){
                    var output=(what.toString().length==1)? "0"+what : what
                    return output
                }

                function displaytime(){
                    serverdate.setSeconds(serverdate.getSeconds()+1)
                    var datestring=montharray[serverdate.getMonth()]+" "+padlength(serverdate.getDate())+", "+serverdate.getFullYear()
                    var timestring=padlength(serverdate.getHours())+":"+padlength(serverdate.getMinutes())+":"+padlength(serverdate.getSeconds())
                    document.getElementById("servertime").innerHTML=datestring+" "+timestring
                }

                window.onload=function(){
                    setInterval("displaytime()", 1000)
                }
            
            </script>
            
        </div>
	</div><!-- header -->

	<div id="mainmenu">
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'Home', 'url'=>array('/site/index')),
                array('label'=>'Newsletters', 'url'=>array('/Newsletters/index'), 'visible'=>!Yii::app()->user->isGuest),
                array('label'=>'Lists', 'url'=>array('/RecipientLists/index'), 'visible'=>!Yii::app()->user->isGuest),
                array('label'=>'Templates', 'url'=>array('/Templates/index'), 'visible'=>!Yii::app()->user->isGuest),
                array('label'=>'Outgoings', 'url'=>array('/Outgoings/index'), 'visible'=>!Yii::app()->user->isGuest),                
                //array('label'=>'Users', 'url'=>array('/Users/index'), 'visible'=>!Yii::app()->user->isGuest),
                array('label'=>'Files', 'url'=>array('/Files/index'), 'visible'=>Yii::app()->user->canCreate),
                array('label'=>'Admin', 'url'=>array('/site/admin'), 'visible'=>Yii::app()->user->isControl),
                //array('label'=>'About', 'url'=>array('/site/page', 'view'=>'about')),
				//array('label'=>'Contact', 'url'=>array('/site/contact')),
				//array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
				//array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest)
			),
		)); ?>
	</div><!-- mainmenu -->
	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif?>

	<?php echo $content; ?>

	<div class="clear"></div>

	<div id="footer">
		Copyright &copy; <?php echo date('Y'); ?> CPSU SPSF Group Victorian Branch & Jason Cleeland.<br/>
        Licensed under the GPL, <?php echo Yii::powered(); ?>
	</div><!-- footer -->

</div><!-- page -->

<?php //print_r($this->auth); ?>

</body>
</html>
