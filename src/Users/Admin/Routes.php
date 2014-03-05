<?php

namespace Users\Admin;

/**
 * Group class is used to keep track of a group of routes with similar aspects (the same controller, the same f3-app and etc)
 */
class Routes extends \Dsc\Routes\Group{
	
	
	function __construct(){
		parent::__construct();
	}
	
	/**
	 * Initializes all routes for this group
	 * NOTE: This method should be overriden by every group
	 */
	public function initialize(){
		$this->setDefaults(
				array(
					'namespace' => '\Users\Admin\Controllers',
					'url_prefix' => '/admin'
				)
		);
		
        // settings routes
		$this->addSettingsRoutes( '/users' );
		
		$this->addCrudGroup( 'Users', 'User' );
		$this->addCrudGroup( 'Groups', 'Group',
							array( 'prefix_url' => '/users/groups' ),
							array( 'prefix_url' => '/users/group' )
		 );
		
        $this->add( '/users/groups/checkboxes', array( 'GET', 'POST' ),
        		array(
        				'controller' => 'Categories',
        				'action' => 'getCheckboxes'
        		)
        );
        
		$this->addCrudGroup( 'Roles', 'Role',
							array( 'prefix_url' => '/users/roles' ),
							array( 'prefix_url' => '/users/role' )
		 );
	}
}