<?php

class DummyModel extends CFormModel {
    public $icsContent;
    public $dtStamp;
    public $eventAttachmentType;
    public $eventTitle;
    public $eventStart;
    public $eventEnd;
    public $uid;
    public $eventOrganiserName;
    public $eventOrganiserEmail;
    public $eventLocation;
    public $eventDescription;

    // Define rules if needed, for example:
    public function rules() {
        return array(
            array('dtStamp, eventAttachmentType, eventTitle, eventStart, eventEnd, uid, eventOrganiserName, eventOrganiserEmail, eventLocation, eventDescription', 'safe'),
        );
    }
}
