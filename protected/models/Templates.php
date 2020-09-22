<?php

/**
 * This is the model class for table "templates".
 *
 * The followings are the available columns in table 'templates':
 * @property integer $id
 * @property string $name
 * @property string $header_html
 * @property string $footer_html
 * @property string $created_on
 * @property string $modified_on
 * @property string $thumb_img
 * @property string $thumb_name
 * @property string $thumb_type
 */
class Templates extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'templates';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, html', 'required'),
			array('name', 'length', 'max'=>255),
			array('modified, created', 'safe'),
            //Set the created and modified fields automatically on insert
            array('created, modified', 'default', 'value'=>new CDbExpression('NOW()'), 'setOnEmpty'=>false, 'on'=>'insert'),
            //Set the modified date on updates
            array('modified', 'default', 'value'=>new CDbExpression('NOW()'), 'setOnEmpty'=>false, 'on'=>'update'),

			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, html, created, modified, thumb_img', 'safe', 'on'=>'search'),
			array ('footer_html', 'safe'),
			array('thumb_img', 'file', 'types'=>'jpg, gif, png, jpeg',
            	'maxSize'=>1024 * 1024 * 1, // 1MB
                'tooLarge'=>'The file was larger than 1MB. Please upload a smaller file.',
            	'allowEmpty' => true ),
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
            'newsletters'=>array(self::HAS_MANY, 'Newsletters', 'templatesId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Template Name',
			'html' => 'Template HTML',
			'created' => 'Created',
			'modified' => 'Modified',
			'thumb_img' => 'Thumb Img'
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
		$criteria->compare('name',$this->name,true);
		//$criteria->compare('header_html',$this->header_html,true);
		//$criteria->compare('footer_html',$this->footer_html,true);
		//$criteria->compare('created',$this->created,true);
		//$criteria->compare('modified',$this->modified,true);
		//$criteria->compare('thumb_img',$this->thumb_img,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Templates the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
