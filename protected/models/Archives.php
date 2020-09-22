<?php
/**
 * This is the model class for table "archives".
 *
 * The followings are the available columns in table 'newsletters':
 * @property integer $id
 * @property integer $newslettersId
 * @property integer $totalSent
 * @property integer $totalRead
 * @property string $totalLinks
 * @property string $dateArchived
 * @property string $recipientEmails
 * @property integer $recipientIds
 * @property integer $created
 * @property integer $modified
 */
class Archives extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public $countArchives;
    
    public function tableName()
    {
        return 'archives';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('newslettersId, totalSent, totalRead, totalLinks, dateArchived', 'required'),
            //Set the created and modified fields automatically on insert
            array('created, modified', 'default', 'value'=>new CDbExpression('NOW()'), 'setOnEmpty'=>false, 'on'=>'insert'),
            //Set the modified date on updates
            array('modified', 'default', 'value'=>new CDbExpression('NOW()'), 'setOnEmpty'=>false, 'on'=>'update'),

            array('newslettersId, totalSent, totalRead, totalLinks', 'numerical', 'integerOnly'=>true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, usersId, recipientListsId, templatesId, title, subject, content, sendDate, completed, completed_html, recipientSql, recipientValues, archive, trackReads, trackLinks, trackBounces, recipientCount, created, modified', 'safe', 'on'=>'search'),
            
            //Make sure these fields have a rule, or else they won't save
            array('newslettersId,totalSent,totalRead,totalLinks,dateArchived,recipientEmails,recipientIds,created,modified', 'safe'),
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
            'newslettersId' => 'Newsletter',
            'totalSent' => 'Total Recipients',
            'totalRead' => 'Total Reads',
            'totalLinks' => 'Total Link Clicks',
            'dateArchived' => 'Date Archived',
            'recipientEmails' => 'Recipient Emails',
            'recipientIds' => 'Recipient Ids',
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
        $criteria->compare('newslettersId',$this->newslettersId);
        $criteria->compare('totalSent',$this->totalSent);
        $criteria->compare('totalRead',$this->totalRead);
        $criteria->compare('totalLinks',$this->totalLinks);
        $criteria->compare('dateArchived',$this->dateArchived);
        $criteria->compare('recipientEmails',$this->recipientEmails);
        $criteria->compare('recipientIds',$this->recipientIds,true);
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
?>
