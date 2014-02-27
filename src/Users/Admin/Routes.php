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
		
		//users list
		$this->addCrudList( 'Users' );
				
		//user crud
		$this->addCrudItem( 'User' );

        // groups list
        $this->addCrudList( 'Groups', array( 'prefix_url' => '/users/groups' ) );
		
        // groups crud
        $this->addCrudItem( 'Group', array( 'prefix_url' => '/users/group' ) );

	}
}