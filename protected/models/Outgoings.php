<?php

/**
 * This is the model class for table "outgoings".
 *
 * The followings are the available columns in table 'outgoings':
 * @property integer $id
 * @property integer $newslettersId
 * @property integer $recipientListsId
 * @property string $recipientId
 * @property string $email
 * @property string $sendDate
 * @property string $dateSent
 * @property integer $sent
 * @property integer $bounce
 * @property string $bounceText
 * @property integer $read
 * @property integer $linkUsed
 */
class Outgoings extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'outgoings';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('newslettersId, recipientListsId, recipientId, sendDate,', 'required'),
			array('newslettersId, recipientListsId, sent, bounce, read, linkUsed', 'numerical', 'integerOnly'=>true),
			array('recipientId, email', 'length', 'max'=>256),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, newslettersId, recipientListsId, recipientId, email, sendDate, dateSent, sent, bounce, bounceText, read, linkUsed', 'safe', 'on'=>'search'),
		    array('email, queueDate, dateSent, sendFailures, sendFailureText, readTime, linkUsedTime, link, data, linkUsed', 'safe'),
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
            'newsletters'=>array(self::BELONGS_TO, 'Newsletters', 'newslettersId' ),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'newslettersId' => 'Newsletter ID',
			'recipientListsId' => 'Recipient List ID',
			'recipientId' => 'Recipient ID',
			'email' => 'Email',
			'sendDate' => 'Send Date',
            'queueDate' => 'Date Queued',
			'dateSent' => 'Date Sent',
            'sendFailures' => 'Send Failures',
            'sendFailureText' => 'Send Faiure Text',
			'sent' => 'Sent',
			'bounce' => 'Bounce',
			'bounceText' => 'Bounce Text',
			'read' => 'Read',
            'readTime' => 'Time Read',
			'linkUsedTime' => 'Link Used',
            'link' => 'Link',
            'data' => 'Data'
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
		$criteria->compare('newslettersId',$this->newslettersId);
		$criteria->compare('recipientListsId',$this->recipientListsId);
		$criteria->compare('recipientId',$this->recipientId,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('sendDate',$this->sendDate,true);
        $criteria->compare('queueDate', $this->queueDate,true);
		$criteria->compare('dateSent',$this->dateSent,true);
		$criteria->compare('sent',$this->sent);
		$criteria->compare('bounce',$this->bounce);
		$criteria->compare('bounceText',$this->bounceText,true);
		$criteria->compare('read',$this->read);
		$criteria->compare('linkUsed',$this->linkUsed);
        $criteria->compare('data', $this->data);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Outgoings the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
