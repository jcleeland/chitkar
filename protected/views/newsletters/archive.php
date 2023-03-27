<?php
/* @var $this NewslettersController */
/* @var $model Newsletters */
$baseUrl=Yii::app()->baseUrl;
$this->breadcrumbs=array(
    'Newsletters'=>array('index'),
    'Archive',
);  
?>
<h1>Archiving</h1>
<?php
    foreach($content as $item) {
    ?>
    <p>Your newsletter(s) have/has been archived and entries in the Outgoings table for this newsletter have been deleted.</p>
    <p style='text-align: center'>
        <a href='?r=Newsletters/index&completed=sent'>Return to newsletters</a>
    </p>    
    <table>
        <tr>
            <td>
                Newsletter Title
            </td>
            <td>
                <?php echo $item['title'] ?> (id: <?php echo $item['id'] ?>)
            </td>
        </tr>
        <tr>
            <td>
                Number of Recipients
            </td>
            <td>
                <?php echo $item['sent'] ?>
            </td>
        </tr>
        <tr>
            <td>
                Number of confirmed reads
            </td>
            <td>
                <?php echo $item['read'] ?>
            </td>
        </tr>
        <tr>
            <td>
                Number of times links clicked
            </td>
            <td>
                <?php echo $item['links'] ?>
            </td>
        </tr>
    </table> 
    <?php       
    }
?>
<p style='text-align: center'>
    <a href='?r=Newsletters/index&completed=sent'>Return to newsletters</a>
</p>
