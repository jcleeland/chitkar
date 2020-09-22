<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
     
    private $_id;
    
    //Declaring here ensures that they exist, even if not set
    public $lastname, $firstname;
    
    
	public function authenticate()
	{
        //Check if the Users table contains any actual users (if not, then allow a default admin login)
        if(Users::model()->count() > 0) {
            $username=strtolower($this->username);
            $user=Users::model()->find('LOWER(username)=?', array($username));
            if($user===null) 
            {
                $this->errorCode=self::ERROR_USERNAME_INVALID;
            } else if (!$user->validatePassword($this->password)) 
            {
                $this->errorCode=self::ERROR_PASSWORD_INVALID;
            } else
            {
                $this->_id=$user->id;
                $this->username=$user->username;
                $this->setState('firstname', $user->firstname);
                $this->setState('lastname', $user->lastname);
                $this->setState('can_create', $user->can_create);
                $this->setState('can_queue', $user->can_queue);
                $this->setState('can_delete', $user->can_delete);
                $this->setState('can_control', $user->can_control);
                $this->setState('can_admin', $user->can_admin);
                $this->setState('roles', 'admin'); //Until more detailed RBAC is developed, everyone is an admin
                $this->errorCode=self::ERROR_NONE;
            }
            return $this->errorCode==self::ERROR_NONE;            
        } else {
            $users=array(
                // username => password
                'demo'=>'demo',
                'admin'=>'admin',
            );
            if(!isset($users[$this->username]))
                $this->errorCode=self::ERROR_USERNAME_INVALID;
            else if($users[$this->username]!==$this->password)
                $this->errorCode=self::ERROR_PASSWORD_INVALID;
            else
            {
                $this->errorCode=self::ERROR_NONE;
                $this->_id=0;
                $this->username=$this->username;
                $this->setState('firstname', "John");
                $this->setState('lastname', "Citizen");
                $this->setState('can_create', 1);
                $this->setState('can_queue', 1);
                $this->setState('can_delete', 1);
                $this->setState('can_control', 1);
                $this->setState('can_admin', 1);
            }
            return !$this->errorCode;        
        }
        
    }
    
    public function getId()
    {
        return $this->_id;
    }
    

    
}