<?php 
namespace Users\Lib\Acl;

abstract class Adapter 
{
    protected $default_access = null;
    
    /**
     * Sets the default access level (\Users\Lib\Acl::ALLOW or \Users\Lib\Acl::DENY)
     * 
     * @param int $defaultAccess
     */    
    public function setDefaultAccess($defaultAccess) 
    {
    	$this->default_access = $defaultAccess;
    }
    
    /**
     * Returns the default ACL access level
     */
    public function getDefaultAccess()
    {
    	return $this->default_access;
    }
    
    /**
     * Returns the role which the list is checking if it’s allowed to certain resource/access
     */
    public function getActiveRole()
    {
    	
    }
    
    /**
     * Returns the resource which the list is checking if some role can access it 
     */
    public function getActiveResource()
    {
    	
    }

    /**
     * Returns the action which the list is checking if some role can access it 
     */
    public function getActiveAction()
    {
    	
    }
}
?>