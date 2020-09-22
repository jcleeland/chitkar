<?php

/**
 * This is the model class for table "statistics".
 *
 * The followings are the available columns in table 'statistics':
 * @property integer $id
 * @property string $date
 * @property integer $newslettersQueued
 * @property integer $emailsSent
 * @property integer $emailsRead
 * @property integer $emailBounces
 * @property integer $linksUsed
 */
class Statistics extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'statistics';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('date, newslettersQueued, emailsSent, emailsRead, emailBounces, linksUsed', 'required'),
			array('newslettersQueued, emailsSent, emailsRead, emailBounces, linksUsed', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, date, newslettersQueued, emailsQueued, emailsSent, emailsRead, emailBounces, linksUsed', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'date' => 'Date',
			'newslettersQueued' => 'Newsletters Queued',
            'emailsQueued' => 'Emails Queued',
			'emailsSent' => 'Emails Sent',
			'emailsRead' => 'Emails Read',
			'emailBounces' => 'Email Bounces',
			'linksUsed' => 'Links Used',
		);
	}

    public function countSentByDate($date) {
        if(!$date) {return 0;}
        $startdate=date("Y-m-d H:i", mktime(0, 0, 0, date("m", $date), date("d", $date), date("Y", $date)));            
        $enddate=date("Y-m-d H:i", mktime(23, 59, 59, date("m", $date), date("d", $date), date("Y", $date)));
        $sql = "SELECT count(id) 
                FROM outgoings 
                WHERE `sent`=1 
                AND dateSent > '$startdate' 
                AND dateSent < '$enddate'";
        $output = Yii::app()->db->createCommand($sql)->queryScalar();
        return $output;
    }
    /**
    * Counts the number of "queued" emails according to date
    * Note: updated so that it also includes emails sent on a particular day
    * since obviously if an email has been sent, it has also been queued.
    * 
    * @param mixed $date
    */
    public function countQueuedByDate($date) {
        if(!$date) {return 0;}
        $startdate=date("Y-m-d H:i", mktime(0, 0, 0, date("m", $date), date("d", $date), date("Y", $date)));            
        $enddate=date("Y-m-d H:i", mktime(23, 59, 59, date("m", $date), date("d", $date), date("Y", $date)));
        $sql = "SELECT count(id) 
                FROM outgoings 
                WHERE queueDate > '$startdate' 
                AND queueDate < '$enddate'";
        $senttoday=$this->countSentByDate($date);
        $output = Yii::app()->db->createCommand($sql)->queryScalar();
        $output+=$senttoday;
        return $output;
    }

    public function countReadByDate($date, $newsletterId='%') {
        if(!$date) {return 0;}
        $startdate=date("Y-m-d H:i", mktime(0, 0, 0, date("m", $date), date("d", $date), date("Y", $date)));            
        $enddate=date("Y-m-d H:i", mktime(23, 59, 59, date("m", $date), date("d", $date), date("Y", $date)));
        $sql = "SELECT count(id) 
                FROM outgoings 
                WHERE `read`=1 
                AND readTime > '$startdate' 
                AND readTime < '$enddate' 
                AND newslettersId LIKE '$newsletterId'";
        $output = Yii::app()->db->createCommand($sql)->queryScalar();
        return $output;
    }
    
    public function countBounceByDate($date, $newsletterId='%') {
        if(!$date) {return 0;}
        $startdate=date("Y-m-d H:i", mktime(0, 0, 0, date("m", $date), date("d", $date), date("Y", $date)));            
        $enddate=date("Y-m-d H:i", mktime(23, 59, 59, date("m", $date), date("d", $date), date("Y", $date)));
        $sql = "SELECT count(id) 
                FROM outgoings 
                WHERE `bounce`=1 
                AND bounceTime > '$startdate' 
                AND bounceTime < '$enddate' 
                AND newslettersId LIKE '$newsletterId'";
        $output = Yii::app()->db->createCommand($sql)->queryScalar();
        return $output;
    }

    public function countLinkUsedByDate($date, $newsletterId='%') {
        if(!$date) {return 0;}
        $startdate=date("Y-m-d H:i", mktime(0, 0, 0, date("m", $date), date("d", $date), date("Y", $date)));            
        $enddate=date("Y-m-d H:i", mktime(23, 59, 59, date("m", $date), date("d", $date), date("Y", $date)));
        $sql = "SELECT count(id) 
                FROM outgoings 
                WHERE `linkUsed`=1 
                AND linkUsedTime > '$startdate' 
                AND linkUsedTime < '$enddate' 
                AND newslettersId LIKE '$newsletterId'";
        $output = Yii::app()->db->createCommand($sql)->queryScalar();
        return $output;
    }

    public function countNewslettersByDate($date) {
        if(!$date) {return 0;}
        $startdate=date("Y-m-d H:i", mktime(0, 0, 0, date("m", $date), date("d", $date), date("Y", $date)));            
        $enddate=date("Y-m-d H:i", mktime(23, 59, 59, date("m", $date), date("d", $date), date("Y", $date)));
        $sql = "SELECT count(id) 
                FROM newsletters
                WHERE `queued`=1 
                AND sendDate > '$startdate' 
                AND sendDate < '$enddate'";
        $output = Yii::app()->db->createCommand($sql)->queryScalar();
        return $output;
    }
    
    public function countSentByDates($startdate, $enddate) {
        $sql = "SELECT count(id) 
                FROM outgoings 
                WHERE `sent`=1 
                AND dateSent > '$startdate' 
                AND dateSent < '$enddate'";
        $output = Yii::app()->db->createCommand($sql)->queryScalar();
        return $output;
    }
    
    public function countQueuedByDates($startdate, $enddate) {
        $sql = "SELECT count(id) 
                FROM outgoings 
                WHERE queueDate > '$startdate' 
                AND queueDate < '$enddate'";
        $output = Yii::app()->db->createCommand($sql)->queryScalar();
        return $output;
    }

    public function countReadByDates($startdate, $enddate, $newsletterId='%') {
        $sql = "SELECT count(id) 
                FROM outgoings 
                WHERE `read`=1 
                AND readTime > '$startdate' 
                AND readTime < '$enddate' 
                AND newslettersId LIKE '$newsletterId'";
        $output = Yii::app()->db->createCommand($sql)->queryScalar();
        return $output;
    }
    
    public function countBounceByDates($startdate, $enddate, $newsletterId='%') {
        $sql = "SELECT count(id) 
                FROM outgoings 
                WHERE `bounce`=1 
                AND bounceTime > '$startdate' 
                AND bounceTime < '$enddate' 
                AND newslettersId LIKE '$newsletterId'";
        $output = Yii::app()->db->createCommand($sql)->queryScalar();
        return $output;
    }

    public function countLinkUsedByDates($startdate, $enddate, $newsletterId='%') {
        $sql = "SELECT count(id) 
                FROM outgoings 
                WHERE `linkUsed`=1 
                AND linkUsedTime > '$startdate' 
                AND linkUsedTime < '$enddate' 
                AND newslettersId LIKE '$newsletterId'";
        $output = Yii::app()->db->createCommand($sql)->queryScalar();
        return $output;
    }

    public function countNewslettersByDates($startdate, $enddate) {
        $sql = "SELECT count(id) 
                FROM newsletters
                WHERE `queued`=1 
                AND sendDate > '$startdate' 
                AND sendDate < '$enddate'";
        $output = Yii::app()->db->createCommand($sql)->queryScalar();
        return $output;
    }
    
    public function averageReadGapByNewsletter($newsletterId) {
        $sql = "SELECT dateSent, readTime
                FROM outgoings
                WHERE sent=1
                AND `read`=1
                AND newslettersId = $newsletterId";
        $output = Yii::app()->db->createCommand($sql)->queryAll();
        $timetoread=0;
        foreach($output as $row) {
            $timetoread+=strtotime($row['readTime'])-strtotime($row['dateSent']);
        }
        if(count($output)) {
            $atr=$timetoread/count($output);
            $atr=Globals::secondsToTime($atr, "short");
        } else {
            $atr="Not available";
        }
        return $atr;
    }
    
    public function medianReadGapByNewsletter($newsletterId) {
        $sql = "SELECT dateSent, readTime
                FROM outgoings
                WHERE sent=1
                AND `read`=1
                AND newslettersId = $newsletterId";
        $output = Yii::app()->db->createCommand($sql)->queryAll();
        foreach($output as $row) {
            $data[]=strtotime($row['readTime'])-strtotime($row['dateSent']);
        }
        if(count($output)) {
            $return=Globals::calculate_median($data);
            $return=Globals::secondsToTime($return, "short");
        } else {
            $return="Not available";
        }
        return  $return;      
    }
    
    public function quartilesReadGapByNewsletter($newsletterId) {
        $sql = "SELECT dateSent, readTime
                FROM outgoings
                WHERE sent=1
                AND `read`=1
                AND newslettersId = $newsletterId";
        $output = Yii::app()->db->createCommand($sql)->queryAll();
        foreach($output as $row) {
            $data[]=strtotime($row['readTime'])-strtotime($row['dateSent']);
        }
        if(count($output)) {
            $return=Globals::calculate_quartiles($data);
            /* foreach($returns as $key=>$returnitem) {
                $return[$key]=Globals::secondsToTime($returnitem, "short");
            } */
        } else {
            $return=array("first"=>null, 
                          "second"=>null, 
                          "third"=>null, 
                          "firstcount"=>null, 
                          "secondcount"=>null, 
                          "thirdcount"=>null);
        }
        return  $return;          
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
		$criteria->compare('date',$this->date,true);
		$criteria->compare('newslettersQueued',$this->newslettersQueued);
		$criteria->compare('emailsSent',$this->emailsSent);
		$criteria->compare('emailsRead',$this->emailsRead);
		$criteria->compare('emailBounces',$this->emailBounces);
		$criteria->compare('linksUsed',$this->linksUsed);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Statistics the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
