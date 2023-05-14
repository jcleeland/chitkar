<?php

/**
 * This is the model class for table "newsletters".
 *
 * The followings are the available columns in table 'newsletters':
 * @property integer $id
 * @property integer $usersId
 * @property integer $recipientListsId
 * @property integer $templatesId
 * @property string $title
 * @property string $content
 * @property string $sendDate
 * @property integer $completed
 * @property string $recipientSql
 * @property string $recipientValues
 * @property integer $archive
 * @property integer $trackReads
 * @property integer $trackLinks
 * @property integer $trackBounces
 * @property integer $recipientCount
 * @property string $created
 * @property string $modified
 */
class Newsletters extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
    public $countNewsletters;
    
	public function tableName()
	{
		return 'newsletters';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('usersId, title, templatesId, recipientSql', 'required'),
            //Set the created and modified fields automatically on insert
            array('created, modified', 'default', 'value'=>new CDbExpression('NOW()'), 'setOnEmpty'=>false, 'on'=>'insert'),
            //Set the modified date on updates
            array('modified', 'default', 'value'=>new CDbExpression('NOW()'), 'setOnEmpty'=>false, 'on'=>'update'),

			array('usersId, recipientListsId, templatesId, queued, completed, archive, trackReads, trackLinks, trackBounces, recipientCount', 'numerical', 'integerOnly'=>true),
			array('recipientValues', 'length', 'max'=>256),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, usersId, recipientListsId, templatesId, title, subject, content, sendDate, completed, completed_html, recipientSql, recipientValues, archive, trackReads, trackLinks, trackBounces, recipientCount, created, modified', 'safe', 'on'=>'search'),
            
            //Make sure these fields have a rule, or else they won't save
            array('recipientListsId,subject,content,sendDate,queued,completed,completed_html,recipientSql,recipientValues,notifications,archive,trackReads,trackBounces,created,modified', 'safe'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
            'users'=>array(self::BELONGS_TO, 'Users', 'usersId' ),
            'recipientLists'=>array(self::BELONGS_TO, 'RecipientLists', 'recipientListsId'),
            'templates'=>array(self::BELONGS_TO, 'Templates', 'templatesId'), 
            'outgoings'=>array(self::HAS_MANY, 'Outgoings', 'newslettersId'),
            'archives'=>array(self::BELONGS_TO, 'Archives', 'id'),
            'fileLinks'=>array(self::HAS_MANY, 'FileLinks', 'newslettersId'), 
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'usersId' => 'Users',
			'recipientListsId' => 'Recipient Lists',
			'templatesId' => 'Templates',
			'title' => 'Title',
            'subject' => 'Subject',
			'content' => 'Content',
			'sendDate' => 'Sent',
            'queued' => 'Queued',
			'completed' => 'Completed',
            'completed_html' => 'Completed HTML',
			'recipientSql' => 'Recipient Sql',
			'recipientValues' => 'Recipient Values',
            'notifications' => 'Notification Emails',
			'archive' => 'Archive',
			'trackReads' => 'Track Reads',
			'trackLinks' => 'Track Links',
			'trackBounces' => 'Track Bounces',
			'recipientCount' => 'Recipient Count',
			'created' => 'Created',
			'modified' => 'Modified',
            'totalSent' => 'Total Sent',
            'totalRead' => 'Total Read',
            'totalLinks' => 'Total Links'  
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('usersId',$this->usersId);
		$criteria->compare('recipientListsId',$this->recipientListsId);
		$criteria->compare('templatesId',$this->templatesId);
		$criteria->compare('title',$this->title,true);
        $criteria->compare('subject',$this->subject,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('sendDate',$this->sendDate,true);
		$criteria->compare('completed',$this->completed);
		$criteria->compare('recipientSql',$this->recipientSql,true);
		$criteria->compare('recipientValues',$this->recipientValues,true);
		$criteria->compare('archive',$this->archive);
		$criteria->compare('trackReads',$this->trackReads);
		$criteria->compare('trackLinks',$this->trackLinks);
		$criteria->compare('trackBounces',$this->trackBounces);
		$criteria->compare('recipientCount',$this->recipientCount);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('modified',$this->modified,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Newsletters the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
