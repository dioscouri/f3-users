<?php
namespace Users\Lib;

/**
 * Users\Lib\Acl
 */
class Acl extends \Dsc\Singleton
{
    const ALLOW = 1;
    const DENY = 0;
     
    /**
     * The ACL Object
     *
     * @var \Users\Lib\Acl\Adapter\Mongo
     */
    private $acl;

    /**
     * Define the resources that are considered "private". These controller => actions require authentication.
     *
     * @var array
     */
    private $privateResources = array();

    /**
     * Human-readable descriptions of the actions used in {@see $privateResources}
     *
     * @var array
     */
    private $actionDescriptions = array(
        'index' => 'Access',
        'search' => 'Search',
        'create' => 'Create',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'changePassword' => 'Change password'
    );

    /**
     * Checks if a controller is private or not
     *
     * @param string $controllerName
     * @return boolean
     */
    public function isPrivate($controllerName)
    {
        return isset($this->privateResources[$controllerName]);
    }

    /**
     * Checks if the current profile is allowed to access a resource
     *
     * @param string $role
     * @param string $controller
     * @param string $action
     * @return boolean
     */
    public function isAllowed($role, $controller, $action)
    {
        return $this->getAcl()->isAllowed($role, $controller, $action);
    }

    /**
     * Returns the ACL list
     *
     * @return Users\Lib\Acl\Adapter\Memory
     */
    public function getAcl()
    {
        // Check if the ACL is already created
        if (is_object($this->acl)) {
            return $this->acl;
        }

        // Check if the ACL is in APC
        /*
        if (function_exists('apc_fetch')) {
            $acl = apc_fetch('phalcon-admin-acl');
            if (is_object($acl) && !empty($acl)) {
                $this->acl = $acl;
                return $acl;
            }
        }*/

        // Check if the ACL is already generated
        // TODO Build the \Dsc\Cache to support engines, such as Mongo, then add it to the DI
        //$data = unserialize( $this->mongo_cache->get('phalcon-admin-acl') );
        $data = null;
        if (empty($data)) 
        {
            $data = $this->rebuild();       	
        }
        
        // TODO Re-enable this
        //$this->mongo_cache->save('phalcon-admin-acl', serialize($acl));
        // Store the ACL in APC
        /*
        if (function_exists('apc_store')) {
            apc_store('phalcon-admin-acl', $this->acl);
        }
        */

        $this->acl = $data;
        
        //$this->mongo_cache->save('phalcon-admin-acl-memory', \Dsc\Lib\Debug::dump($this->acl, false) );

        return $this->acl;
    }

    /**
     * Returns the permissions assigned to a profile
     *
     * @param \Users\Models\Roles $role
     * @return array
     */
    public function getPermissions(\Users\Models\Roles $role)
    {
        $permissions = array();
        foreach ($role->getPermissions() as $permission) {
            $permissions[$permission->resource . '.' . $permission->action] = true;
        }
        return $permissions;
    }

    /**
     * Returns all the resoruces and their actions available in the application
     *
     * @return array
     */
    public function getResources()
    {
        return $this->privateResources;
    }

    /**
     * Returns the action description according to its simplified name
     *
     * @param string $action
     * @return $action
     */
    public function getActionDescription($action)
    {
        if (isset($this->actionDescriptions[$action])) {
            return $this->actionDescriptions[$action];
        } else {
            return $action;
        }
    }

    /**
     * Rebuilds the access list into a file
     *
     * @return \Users\Lib\Acl\Adapter\Memory
     */
    public function rebuild()
    {
        $options = array(
        	'connection' => \Dsc\System::instance()->get('mongo'),
            'roles' => 'acl.roles',
            'resources' => 'acl.resources',
            'resourceActions' => 'acl.resourceActions',
            'access' => 'acl.access'
        );
        $acl = new \Users\Lib\Acl\Adapter\Mongo($options);
        
        $acl->setDefaultAccess(\Users\Lib\Acl::DENY);

        // Register roles
        $roles = (new \Users\Models\Roles)->getItems();

        // give root role access to everything
        $acl->addRole(new \Users\Lib\Acl\Role('root'));
        $acl->allow('root', '*', '*');
        $acl->allow('*', 'Admin\Controllers\Home', 'display');
        
        // make sure the system roles exist
        $acl->addRole(new \Users\Lib\Acl\Role('unidentified'));
        $acl->addRole(new \Users\Lib\Acl\Role('identified'));
        
        foreach ($roles as $role) {
            $acl->addRole(new \Users\Lib\Acl\Role($role->slug));
            
            // Grant permissions in "permissions" model
            foreach ($role->getPermissions() as $permission) {
                $acl->allow($role->slug, $permission->resource, $permission->action);
            }
            
            // Always grant these permissions
            //$acl->allow($role->title, '\Whatever\The\Users\Controller', 'changePassword');
        }

        return $acl;
    }
}