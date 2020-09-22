<?php
/* @var $this NewslettersController */
/* @var $model Newsletters */
$baseUrl=Yii::app()->baseUrl;
$this->breadcrumbs=array(
    'Newsletters'=>array('index'),
    $content['title'],
);  
?>
<h1><?php echo $content['title']; ?> (<?php echo $content['id'] ?>)</h1>
<p>Your newsletter has been archived and entries in the Outgoings table for this newsletter have been deleted.</p>
<table>
    <tr>
        <td>
            Newsletter Title
        </td>
        <td>
            <?php echo $content['title'] ?>
        </td>
    </tr>
    <tr>
        <td>
            Number of Recipients
        </td>
        <td>
            <?php echo $content['sent'] ?>
        </td>
    </tr>
    <tr>
        <td>
            Number of confirmed reads
        </td>
        <td>
            <?php echo $content['read'] ?>
        </td>
    </tr>
    <tr>
        <td>
            Number of times links clicked
        </td>
        <td>
            <?php echo $content['links'] ?>
        </td>
    </tr>
</table>
<p style='text-align: center'>
    <a href='?r=Newsletters/index'>Return to newsletters</a>
</p>
