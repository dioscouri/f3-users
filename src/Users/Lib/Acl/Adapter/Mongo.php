<?php
namespace Users\Lib\Acl\Adapter;

use Users\Lib\Acl;
use Users\Lib\Acl\Role;

/**
 * Users\Lib\Acl\Adapter\Mongo
 * Manages ACL lists using Mongo Collections
 */
class Mongo extends \Users\Lib\Acl\Adapter implements \Users\Lib\Acl\AdapterInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Class constructor.
     *
     * @param  array                  $options
     * @throws \Users\Lib\Acl\Exception
     */
    public function __construct($options)
    {
        if (!is_array($options)) {
            throw new \Exception("Acl options must be an array");
        }

        if (!isset($options['connection'])) {
            throw new \Exception("Parameter 'connection' is required");
        }
        
        if (!isset($options['roles'])) {
            throw new \Exception("Parameter 'roles' is required");
        }

        if (!isset($options['resources'])) {
            throw new \Exception("Parameter 'resources' is required");
        }

        if (!isset($options['resourceActions'])) {
            throw new \Exception("Parameter 'resourceActions' is required");
        }

        if (!isset($options['access'])) {
            throw new \Exception("Parameter 'access' is required");
        }

        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     * Example:
     * <code>$acl->addRole(new Users\Lib\Acl\Role('administrator'), 'consultor');</code>
     * <code>$acl->addRole('administrator', 'consultor');</code>
     *
     * @param  string  $role
     * @param  array   $accessInherits
     * @return boolean
     */
    public function addRole($role, $accessInherits = null)
    {
        if (!is_object($role)) {
            $role = new Role($role);
        }

        $roles = $this->getCollection('roles');
        $exists = $roles->count(array('title' => $role->getName()));

        if (!$exists) {
            (new \Users\Models\Roles)->insert(
                array(
                    'title'        => $role->getName(),
                    'description' => $role->getDescription()
                )        	
            );
            /*
            $roles->insert(array(
                'name'        => $role->getName(),
                'description' => $role->getDescription()
            ));
            */
            /* Is this necessary when the default action is to deny??
            $this->getCollection('access')->insert(array(
                'roles_name'     => $role->getName(),
                'resource_name' => '*',
                'action_name'    => '*',
                'allowed'        => $this->getDefaultAccess()
            ));
            */
        }

        if ($accessInherits) {
            return $this->addInherit($role->getName(), $accessInherits);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string                 $roleName
     * @param  string                 $roleToInherit
     * @throws \Users\Lib\Acl\Exception
     */
    public function addInherit($roleName, $roleToInherit)
    {
        // TODO implement later, after Roles become a nested set
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $roleName
     * @return boolean
     */
    public function isRole($roleName)
    {
        if ($roleName == '*') {
        	return true;
        }
        return $this->getCollection('roles')->count(array('slug' => $roleName)) > 0;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $resourceName
     * @return boolean
     */
    public function isResource($resourceName)
    {
        if ($resourceName == '*') {
            return true;
        }        
        return $this->getCollection('resources')->count(array('name' => $resourceName)) > 0;
    }

    /**
     * {@inheritdoc}
     * Example:
     * <code>
     * //Add a resource to the the list allowing access to an action
     * $acl->addResource(new Users\Lib\Acl\Resource('customers'), 'search');
     * $acl->addResource('customers', 'search');
     * //Add a resource  with an access list
     * $acl->addResource(new Users\Lib\Acl\Resource('customers'), array('create', 'search'));
     * $acl->addResource('customers', array('create', 'search'));
     * </code>
     *
     * @param  \Users\Lib\Acl\Resource $resource
     * @param  array|string          $actions
     * @return boolean
     */
    public function addResource($resource, array $actions = null)
    {
        if (!is_object($resource)) {
            $resource = new \Users\Lib\Acl\Resource($resource);
        }

        $resources = $this->getCollection('resources');

        $exists = $resources->count(array('name' => $resource->getName()));
        if (!$exists) {
            $resources->insert(array(
                'name'        => $resource->getName(),
                'description' => $resource->getDescription()
            ));
        }

        if ($actions) {
            return $this->addResourceAction($resource->getName(), $actions);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string                 $resourceName
     * @param  array|string           $actions
     * @return boolean
     * @throws \Users\Lib\Acl\Exception
     */
    public function addResourceAction($resourceName, $actions)
    {
        if (!$this->isResource($resourceName)) {
            $this->addResource($resourceName);
            //throw new \Exception("Resource '" . $resourceName . "' does not exist in ACL");
        }

        $resourceActions = $this->getCollection('resourceActions');

        if (is_array($actions)) {
            foreach ($actions as $actionName) {
                $exists = $resourceActions->count(array(
                    'resource_name' => $resourceName,
                    'action_name'    => $actionName
                ));
                if (!$exists) {
                    $resourceActions->insert(array(
                        'resource_name' => $resourceName,
                        'action_name'    => $actionName
                    ));
                }
            }
        } else {
            $exists = $resourceActions->count(array(
                'resource_name' => $resourceName,
                'action_name'    => $actions
            ));
            if (!$exists) {
                $resourceActions->insert(array(
                    'resource_name' => $resourceName,
                    'action_name'    => $actions
                ));
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return \Users\Lib\Acl\Resource[]
     */
    public function getResources()
    {
        $resources = array();
        foreach ($this->getCollection('resources')->find() as $row) {
            $resources[] = new \Users\Lib\Acl\Resource($row['name'], $row['description']);
        }
        
        return $resources;
    }

    /**
     * {@inheritdoc}
     *
     * @return \Users\Lib\Acl\Resource[]
     */
    public function getResourceActions( $resource )
    {
    	$actions = array();
    	$conditions = array(
    			'resource_name' => $resource,
    	);
    	
    	foreach ($this->getCollection('resourceActions')->find( $conditions ) as $row) {
    		$actions []= new \Users\Lib\Acl\ResourceAction($row['resource_name'], $row['action_name']);
    	}
    
    	return $actions;
    }
    
    /**
     * {@inheritdoc}
     *
     * @return \Users\Lib\Acl\Role[]
     */
    public function getRoles()
    {
        $roles = array();

        foreach ($this->getCollection('roles')->find() as $row) {
            $roles[] = new Role($row['title'], $row['description']);
        }

        return $roles;
    }

    /**
     * {@inheritdoc}
     *
     * @param string       $resourceName
     * @param array|string $actions
     */
    public function dropResourceAction($resourceName, $actions)
    {
    }

    /**
     * {@inheritdoc}
     * You can use '*' as wildcard
     * Example:
     * <code>
     * //Allow access to guests to search on customers
     * $acl->allow('guests', 'customers', 'search');
     * //Allow access to guests to search or create on customers
     * $acl->allow('guests', 'customers', array('search', 'create'));
     * //Allow access to any role to browse on products
     * $acl->allow('*', 'products', 'browse');
     * //Allow access to any role to browse on any resource
     * $acl->allow('*', '*', 'browse');
     * </code>
     *
     * @param string $roleName
     * @param string $resourceName
     * @param mixed  $access
     */
    public function allow($roleName, $resourceName, $access)
    {
        $this->allowOrDeny($roleName, $resourceName, $access, \Users\Lib\Acl::ALLOW);
    }

    /**
     * {@inheritdoc}
     * You can use '*' as wildcard
     * Example:
     * <code>
     * //Deny access to guests to search on customers
     * $acl->deny('guests', 'customers', 'search');
     * //Deny access to guests to search or create on customers
     * $acl->deny('guests', 'customers', array('search', 'create'));
     * //Deny access to any role to browse on products
     * $acl->deny('*', 'products', 'browse');
     * //Deny access to any role to browse on any resource
     * $acl->deny('*', '*', 'browse');
     * </code>
     *
     * @param  string  $roleName
     * @param  string  $resourceName
     * @param  mixed   $access
     * @return boolean
     */
    public function deny($roleName, $resourceName, $access)
    {
        $this->allowOrDeny($roleName, $resourceName, $access, \Users\Lib\Acl::DENY);
    }

    /**
     * {@inheritdoc}
     * Example:
     * <code>
     * //Does Andres have access to the customers resource to create?
     * $acl->isAllowed('Andres', 'Products', 'create');
     * //Do guests have access to any resource to edit?
     * $acl->isAllowed('guests', '*', 'edit');
     * </code>
     *
     * @param  string  $role
     * @param  string  $resource
     * @param  string  $access
     * @return boolean
     */
    public function isAllowed($role, $resource, $action)
    {
        $actions = $this->getCollection('access');

        /**
         * Check if there is an specific rule for that role and this resource + action
         */
        $access     = $actions->findOne(array(
            'roles_name'     => $role,
            'resource_name' => $resource,
            'action_name'    => $action
        ));
        if (is_array($access)) {
            return (bool) $access['allowed'];
        }
        /**
         * Check if there is a rule for all roles covering this resources + action
         */
        $access = $actions->findOne(array(
            'roles_name'     => '*',
            'resource_name' => $resource,
            'action_name'    => $action
        ));
        if (is_array($access)) {
            return (bool) $access['allowed'];
        }
        
        /**
         * Check if there is a rule for this role and that resource + *
         */
        $access = $actions->findOne(array(
                        'roles_name'     => $role,
                        'resource_name' => $resource,
                        'action_name'    => '*'
        ));
        if (is_array($access)) {
            return (bool) $access['allowed'];
        }
       
        /**
         * Check if there is a rule for all roles covering this resource
         */
        $access = $actions->findOne(array(
            'roles_name'     => '*',
            'resource_name' => $resource,
            'action_name'    => '*'
        ));
        
        if (is_array($access)) {
            return (bool) $access['allowed'];
        }

        /**
         * Check if there is a rule for that role covering all resources + actions
         */
        $access = $actions->findOne(array(
                        'roles_name'     => $role,
                        'resource_name' => '*',
                        'action_name'    => '*'
        ));
        if (is_array($access)) {
            return (bool) $access['allowed'];
        }
        
        return $this->getDefaultAccess();
    }

    /**
     * Returns a mongo collection
     *
     * @param  string           $name
     * @return \MongoCollection
     */
    protected function getCollection($name)
    {
        return $this->options['connection']->selectCollection($this->options[$name]);
    }

    /**
     * Inserts/Updates a permission in the access list
     *
     * @param  string                 $roleName
     * @param  string                 $resourceName
     * @param  string                 $actionName
     * @param  integer                $action
     * @return boolean
     * @throws \Users\Lib\Acl\Exception
     */
    protected function insertOrUpdateAccess($roleName, $resourceName, $actionName, $action, $autoInsert=true)
    {
        /**
         * Check if the access is valid in the resource
         */
        $exists = $this->getCollection('resourceActions')->count(array(
            'resource_name' => $resourceName,
            'action_name'    => $actionName
        ));
        if (!$exists && ( $resourceName != '*' && $actionName != '*') ) {
            if ($autoInsert) {
                $this->addResourceAction($resourceName, $actionName);                
            } else {
                throw new \Exception(
                        "Access '" . $actionName . "' does not exist in resource '" . $resourceName . "' in ACL"
                );
            }
        }

        $actions = $this->getCollection('access');

        $access = $actions->findOne(array(
            'roles_name'     => $roleName,
            'resource_name' => $resourceName,
            'action_name'    => $actionName
        ));
        if (!$access) {
            $actions->insert(array(
                'roles_name'     => $roleName,
                'resource_name' => $resourceName,
                'action_name'    => $actionName,
                'allowed'        => $action
            ));
        } else {
            $access['allowed'] = $action;
            $actions->save($access);
        }

        /**
         * Update the access '*' in access_list
         */
        /*
         * This isn't necessary when the default access is to deny
        $exists = $actions->count(array(
            'roles_name'     => $roleName,
            'resource_name' => $resourceName,
            'action_name'    => '*'
        ));
        if (!$exists) {
            $actions->insert(array(
                'roles_name'     => $roleName,
                'resource_name' => $resourceName,
                'action_name'    => '*',
                'allowed'        => $this->getDefaultAccess()
            ));
        }
        */

        return true;
    }

    /**
     * Inserts/Updates a permission in the access list
     *
     * @param  string                 $roleName
     * @param  string                 $resourceName
     * @param  string                 $access
     * @param  integer                $action
     * @throws \Users\Lib\Acl\Exception
     */
    protected function allowOrDeny($roleName, $resourceName, $action, $access)
    {

        if (!$this->isRole($roleName)) {
            throw new \Exception('Role "' . $roleName . '" does not exist in the list');
        }
        
        if (is_array($action)) {
            foreach ($action as $actionName) {
                $this->insertOrUpdateAccess($roleName, $resourceName, $actionName, $access);
            }
        } else {
        	$this->insertOrUpdateAccess($roleName, $resourceName, $action, $access);
        }
    }
}