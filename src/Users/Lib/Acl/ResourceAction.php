<?php 
namespace Users\Lib\Acl;

class ResourceAction implements ResourceActionInterface 
{
    protected $resource = null;
    protected $action = null;
    
    /**
     * 
     */
    public function __construct ($resource, $action)
    {
    	$this->resource = $resource;
    	$this->action = $action;
    }
    
    /**
     * Returns the resource name
     */
    public function getResource()
    {
    	return $this->resource;
    }
    
    /**
     * Returns the action
     */
    public function getAction()
    {
        return $this->action;
    }
    
    /**
     * Magic method __toString 
     */
    public function __toString() 
    {
    	return $this->getResource().'.'.$this->getAction();
    }
}
?>