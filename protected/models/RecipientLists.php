<?php

/**
 * This is the model class for table "recipient_lists".
 *
 * The followings are the available columns in table 'recipient_lists':
 * @property integer $id
 * @property string $name
 * @property string $sql
 * @property string $values
 * @property string $keywords
 * @property string $created
 * @property string $modified
 */
class RecipientLists extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'recipient_lists';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
            //Set the created and modified fields automatically on insert
            array('created, modified', 'default', 'value'=>new CDbExpression('NOW()'), 'setOnEmpty'=>false, 'on'=>'insert'),
            //Set the modified date on updates
            array('modified', 'default', 'value'=>new CDbExpression('NOW()'), 'setOnEmpty'=>false, 'on'=>'update'),
			array('name', 'length', 'max'=>128),
            array('library', 'length', 'max'=>50),
			array('keywords', 'length', 'max'=>256),
            array('sql, values', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, sql, values, keywords, created, modified', 'safe', 'on'=>'search'),
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
            'newsletters'=>array(self::HAS_MANY, 'RecipsientList', 'recipientListsId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'List Name',
			'sql' => 'Sql',
			'values' => 'Values',
			'keywords' => 'Keywords',
            'library' => 'Library',
			'created' => 'Created',
			'modified' => 'Modified',
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
		$criteria->compare('sql',$this->sql,true);
		$criteria->compare('values',$this->values,true);
		$criteria->compare('keywords',$this->keywords,true);
        $criteria->compare('library',$this->library,true);
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
	 * @return RecipientLists the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
