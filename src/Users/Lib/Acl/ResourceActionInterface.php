<?php 
namespace Users\Lib\Acl;

interface ResourceActionInterface 
{
    /**
     * 
     */
    public function __construct ($resource, $action);
    
    /**
     * Returns the resource name
     */
    public function getResource();
    
    /**
     * Returns the action
     */
    public function getAction();
    
    /**
     * Magic method __toString 
     */
    public function __toString();
}
?>