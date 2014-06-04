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
    
    private $__list_permissions = array();
    
    protected function beforeValidate(){
    	if( !empty( $this->set_permissions ) ) {
    		$this->__list_permissions = (array) $this->set_permissions;
    		unset( $this->set_permissions );
    	}
    	
    	return parent::beforeValidate();
	}
    
    protected function beforeSave()
    {
        unset($this->cursor);
        
        return parent::beforeSave();
    }
    
    protected  function afterSave(){
    	if( !empty( $this->_list_permissions ) ) {
			$acl = \Dsc\System::instance()->get('acl')->getAcl();
			    	
			foreach( $this->_list_permissions as $resource => $actions ) {
				foreach( $actions as $action => $val ) {
					if( ((int)$val) == 1 ) {
						$acl->allow( $this->slug, $resource, $action );
					} else {
						$acl->deny( $this->slug, $resource, $action );
					}
				}
			}
		}
		    		 
		return parent::afterSave();
    }
    
    public function getPermissions()
    {
        $permissions = array();
        if( empty( $this->slug ) ){
        	return $permissions;	
        }
        
		$acl = \Dsc\System::instance()->get('acl');
		$collection = \Dsc\System::instance()->get('mongo')->selectCollection('acl.access');
		
		$conditions = array(
				'roles_name' => $this->slug,
			);
		
        foreach($collection->find( $conditions ) as $row) {
        	if( (int)$row['allowed'] ) {
	    		$permissions []= new \Users\Lib\Acl\Permission($row['resource_name'], $row['action_name'], (int)$row['allowed'] );
        	}
    	}
   
        return $permissions;
    }
}