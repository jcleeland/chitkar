<?php

/**
 * This is the model class for table "users".
 *
 * The followings are the available columns in table 'users':
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $firstname
 * @property string $lastname
 * @property string $created
 * @property string $modified
 */
class Users extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
    
    public $password_first;
    public $password_repeat;
	
    public function tableName()
	{
		return 'users';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('username, email, firstname, lastname', 'required'),
            array('password_first, password_repeat', 'required', 'on' => 'create'),
            //Set the created and modified fields automatically on insert
            array('created, modified', 'default', 'value'=>new CDbExpression('NOW()'), 'setOnEmpty'=>false, 'on'=>'insert'),
            //Set the modified date on updates
            array('modified', 'default', 'value'=>new CDbExpression('NOW()'), 'setOnEmpty'=>false, 'on'=>'update'),
            
			array('username, password_first, password_repeat, email, firstname, lastname', 'length', 'max'=>128),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, username, email, firstname, lastname, created, modified', 'safe', 'on'=>'search'),
            //Make sure these fields have a rule, or else they won't save
            array('password_repeat, password_first, can_create, can_queue, can_delete, can_control, can_admin', 'safe'),
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
            'newsletters'=>array(self::HAS_MANY, 'Newsletters', 'usersId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'username' => 'Username',
			'password' => 'Password',
            'password_first' => 'Password',
            'password_repeat' => 'Repeat password',
			'email' => 'Email',
			'firstname' => 'Firstname',
			'lastname' => 'Lastname',
            'can_create' => 'Create news',
            'can_queue' => 'Queue news',
            'can_delete' => 'Delete news',
            'can_control' => 'Control queue',
            'can_admin' => 'Administrator',
			'created' => 'Created',
			'modified' => 'Modified',
            'fullname' => 'Chitkar User',
		);
	}
    
    public function getFullName() {
        return $this->firstname.' '.$this->lastname;
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
		$criteria->compare('username',$this->username,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('firstname',$this->firstname,true);
		$criteria->compare('lastname',$this->lastname,true);
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
	 * @return Users the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    public function validatePassword($password)
    {
        if(!defined('CRYPT_BLOWFISH') || !CRYPT_BLOWFISH) {
		    //die(crypt($password, "ch")." -- ".$this->password);
            if(crypt($password, "ch")==$this->password) {
                return true;
            } else {
                return false;
            } 
        } else {
            return CPasswordHelper::verifyPassword($password,$this->password);
        }
    }
    
    public function hashPassword($password)
    {
        if(!defined('CRYPT_BLOWFISH') || !CRYPT_BLOWFISH) {
            return crypt($password, "ch"); 
        } else {
            return CPasswordHelper::hashPassword($password);
        }
    }
}
