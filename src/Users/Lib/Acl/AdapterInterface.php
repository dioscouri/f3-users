<?php 
namespace Users\Lib\Acl;

interface AdapterInterface 
{
    /**
     * Sets the default access level (\Users\Lib\Acl::ALLOW or \Users\Lib\Acl::DENY)
     * 
     * @param unknown $defaultAccess
     */
    public function setDefaultAccess($defaultAccess);
    
    /**
     * Returns the default ACL access level
     * 
     * @return int
     */
    public function getDefaultAccess();

    /**
     * Adds a role to the ACL list. Second parameter lets to inherit access data from other existing role
     * 
     * @param string $role
     * @param string $accessInherits
     * 
     * @return boolean
     */
    public function addRole($role, $accessInherits=null);
    
    /**
     * Do a role inherit from another existing role
     */
    public function addInherit($roleName, $roleToInherit);
    
    /**
     * Check whether role exist in the roles list
     *  
     * @param unknown $roleName
     * 
     * @return boolean
     */
    public function isRole($roleName);
    
    /**
     * Check whether resource exist in the resources list
     *  
     * @param unknown $resourceName
     */
    public function isResource($resourceName);
    
    /**
     * Adds a resource to the ACL list Access names can be a particular action, by example search, update, delete, etc or a list of them
     *  
     * @param unknown $resource
     * @param unknown $accessList
     */
    public function addResource($resource, array $accessList);
    
    /**
     * Adds access to resources
     *  
     * @param unknown $resourceName
     * @param unknown $accessList
     */
    public function addResourceAction($resourceName, $accessList);
    
    /**
     * Removes an access from a resource
     *  
     * @param unknown $resourceName
     * @param unknown $accessList
     */
    public function dropResourceAction($resourceName, $actions);
    
    /**
     * Allow access to a role on a resource
     *  
     * @param unknown $roleName
     * @param unknown $resourceName
     * @param unknown $access
     */
    public function allow ($roleName, $resourceName, $action);
    
    /**
     * Deny access to a role on a resource
     *  
     * @param unknown $roleName
     * @param unknown $resourceName
     * @param unknown $access
     */
    public function deny ($roleName, $resourceName, $action);
    
    /**
     * Check whether a role is allowed to access an action from a resource
     *  
     * @param unknown $role
     * @param unknown $resource
     * @param unknown $access
     */
    public function isAllowed ($role, $resource, $action);
    
    /**
     * Returns the role which the list is checking if it’s allowed to certain resource/access 
     */
    public function getActiveRole();
    
    /**
     * Returns the resource which the list is checking if some role can access it 
     */
    public function getActiveResource();
    
    /**
     * Returns the access which the list is checking if some role can access it
     */
    public function getActiveAction();

    /**
     * Return an array with every role registered in the list 
     */
    public function getRoles();
    
    /**
     * Return an array with every resource registered in the list
     */
    public function getResources();
}
?>
