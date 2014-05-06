<?php 
namespace Users\Models;

class Roles extends \Dsc\Mongo\Collections\Categories 
{
    protected $__collection_name = 'acl.roles';
    protected $__type = 'acl.role';
    protected $__config = array(
        'default_sort' => array(
            'path' => 1
        ),
    );    
    
    protected function beforeSave()
    {
        unset($this->cursor);
        
        return parent::beforeSave();
    }
    
    public function getPermissions()
    {
        $permissions = array();
    
        // TODO Get the permissions for this group, as defined by the admin
    
        return $permissions;
    }
}