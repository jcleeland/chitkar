<?php 
class WebUser extends CWebUser
{
    /**
     * Overrides a Yii method that is used for roles in controllers (accessRules).
     *
     * @param string $operation Name of the operation required (here, a role).
     * @param mixed $params (opt) Parameters for this operation, usually the object to access.
     * @return bool Permission granted?
     */
    
    public function checkAccess($operation, $params=array())
    {
        if (empty($this->id)) {
            // Not identified => no rights
            return false;
        }
        $role = $this->getState("roles");
        if ($role === 'admin') {
            return true; // admin role has access to everything
        }
        // allow access if the operation request is the current user's role
        return ($operation === $role);
    }
    
    public function getIsControl() {
        if($this->hasState('can_control')) {
            return $this->getState('can_control');
        } else {
            return 0;
        }
    }
    
    public function getIsAdmin() {
        if($this->hasState('can_admin')) {
            return $this->getState('can_admin');
        } else {
            return 0;
        }
    }
    
    public function getCanQueue() {
        if($this->hasState('can_queue')) {
            return $this->getState('can_queue');
        } else {
            return 0;
        }
    }

    public function getCanCreate() {
        if($this->hasState('can_create')) {
            return $this->getState('can_create');
        } else {
            return 0;
        }
    }

    public function getCanDelete() {
        if($this->hasState('can_delete')) {
            return $this->getState('can_delete');
        } else {
            return 0;
        }
    }
}