<?php 
namespace Users\Lib\Acl;

class Permission
{
    public $resource = null;
    public $action = null;
    public $allow = null;
    
    /**
     * 
     */
    public function __construct($resource, $action, $allow = 0)
    {
    	$this->resource = $resource;
    	$this->action = $action;
    	$this->allow = $allow;
    }
    
    /**
     * Magic method __toString 
     */
    public function __toString() 
    {
    	return $this->resource.'.'.$this->action;
    }
}
?>