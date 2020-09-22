<?php
/* @var $this RecipientListsController */
/* @var $model RecipientLists */
/* @var $form CActiveForm */
$baseUrl=Yii::app()->baseUrl;
Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/RecipientList.js');
Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/externalDb.js');
Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/formPageProtect.js');

?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'recipient-lists-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>
    
    <div id="DisplayListName"></div>
    <?php //print_r($model) ?>
    <div id="step1" class='page'>
        <p class='pageTitle'>First, give your new recipient list a name, and some searchable keywords</p>
	    <div class='pageContent'>
            <div class="row">
		        <?php echo $form->labelEx($model,'name'); ?>
		        <?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>128)); ?>
		        <?php echo $form->error($model,'name'); ?>
            </div>
            <div class="row">
                <?php echo $form->labelEx($model,'keywords'); ?>
                <?php echo $form->textField($model,'keywords',array('size'=>60,'maxlength'=>256)); ?>
                <?php echo $form->error($model,'keywords'); ?>
            </div>
            <div class="row">
                <?php echo $form->labelEx($model, 'library'); ?>
                <?php echo $form->textField($model, 'library', array('size'=>30, 'maxlength'=>50, 'value'=>$library)); ?>
                <?php echo $form->error($model,'library'); ?>
            </div>
            <input type='button' value='Enter values >>' id='entervalues' />
	    </div>
    </div>
    <div id="dialog-confirm" title="Change the existing SQL?">
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
        If you choose 'Change SQL', any manually created SQL modifications will be lost.</p>
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
        If you choose 'Use original SQL' any changes you've mode to the filters on this page will be ignored.</p>
    </div>
    <div id="step2" style='display: none' class='page'>
        <p class='pageTitle'>Enter or modify the search filter values for your recipient list</p>
        <div class='pageContent'>
            <i>(if you need to use pure SQL, leave these blank and just click 'Generate SQL')</i>
            <div class="row">
            <?php
                foreach($fields as $field=>$properties) {
                    //$fieldnames[]=$field;
                    $fieldnames[]=$properties['fieldname'];
                    $tables[]=$properties['tablename'];
                    $joins[]=$properties['join'];
                    $fieldjoins[]=$properties['fieldjoins'];
                    $inputname=str_replace(' ', '', $properties['tablename']).".".str_replace(' ','', $properties['fieldname']);
                    echo "<div style='float: left; width: 200px'>".$properties['displayname']."</div>";
                    echo "<div style='float: left; width: 100px'><input type='text' size='20' name='$inputname' id='$inputname' class='edb_fields' /></div>\n";
                    echo "<div style='clear: both'></div>\n\n";
                }
                echo "<input type='hidden' id='edb_fieldnames' value='".implode(",", $fieldnames)."' />\n";
                echo "<input type='hidden' id='edb_tables' value='".implode(",", $tables)."' />\n"; 
                echo "<input type='hidden' id='edb_joins' value='".implode(",", $joins)."' />\n";
                echo "<input type='hidden' id='edb_fieldjoins' value='".implode(",", $fieldjoins)."' />\n";
                echo "<input type='hidden' id='edb_sql_joins' value=\"".implode(",", $starters['joins'])."\" />\n";
                echo "<input type='hidden' id='edb_sql_wheres' value=\"".implode(",", $starters['wheres'])."\" />\n";
                echo "<input type='hidden' id='edb_sql_froms' value=\"".implode(",", $starters['froms'])."\" />\n";
                echo "<input type='hidden' id='edb_sql_selects' value=\"".implode(",", $starters['selects'])."\" />\n\n";
            ?>
            <input type='button' value='<< Name and Keywords' id='namekeywords' />
            <input type='button' value='Generate SQL >>' id='generatesql' />
            </div>
        </div>
    </div>
    
    <div id="step3" style='display: none' class='page'>
        <p class='pageTitle'>This is the SQL your Recipient List will use to build a newsletter list. Check it and make modifications if necessary.</p>
        <div class='pageContent'>
            <i>Click 'Test SQL' to check that your SQL is valid, will work, and is generating the right list of people before continuing.</i>
            <div class="row">
		        <?php echo $form->labelEx($model,'sql'); ?>
		        <?php echo $form->textArea($model,'sql',array('rows'=>10, 'cols'=>80, 'style'=>'background-color: silver; font-size: 9pt; font-family: Consolas,Monaco,Lucida Console,Liberation Mono,DejaVu Sans Mono,Bitstream Vera Sans Mono,Courier New;')); ?>
		        <?php echo $form->error($model,'sql'); ?><br />
                <input type='button' value='Test SQL' id='testSQLbtn' />
	        </div>
            <div id='testsql' title='Test SQL'>
                <div id='testsql_results' class='testsql'>
                </div>
            </div>
	        <div class="row" style='display: none'>
		        <?php echo $form->labelEx($model,'values'); ?>
		        <?php echo $form->textArea($model,'values',array('rows'=>6, 'cols'=>50)); ?>
		        <?php echo $form->error($model,'values'); ?>
	        </div>
            <input type='hidden' id='test_sql' value="<?php echo htmlentities($model->sql) ?>" />
            <input type='hidden' name='sqlOK' id='sqlOK' value='0' />
            <input type='button' value='<< Enter values' id='returnentervalues' />
            <input type='button' value='Continue >>' id='reviewlist' />
        </div>
    </div>
    
    <div id="step4" style='display: none' class='page'>
        <p class='pageTitle'>Confirm that your list is correct, and then save it.</p>
        <div class='pageContent'>
	        <div class="row buttons">
                <input type='button' value='<< Edit SQL' id='returnsql' />
		        <?php echo CHtml::submitButton($model->isNewRecord ? 'Create your Recipient List' : 'Save'); ?>
	        </div>
        </div>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->