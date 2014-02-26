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
		$this->add( '/users/settings', 'GET', array(
								'controller' => 'Settings',
								'action' => 'display'
								));

		$this->add( '/users/settings', 'POST', array(
				'controller' => 'Settings',
				'action' => 'save'
		));

		//users list
		$this->add( '/users', array('GET','POST'), array(
				'controller' => 'Users',
				'action' => 'display'
		));

		$this->add( '/users/page/@page', array('GET','POST'), array(
				'controller' => 'Users',
				'action' => 'display'
		));

		$this->add( '/users/delete', array('GET','POST'), array(
				'controller' => 'Users',
				'action' => 'delete'
		));
		
		//user crud
		$this->add( '/user/create', 'GET', array(
				'controller' => 'User',
				'action' => 'create'
		));

		$this->add( '/user/add', 'POST', array(
				'controller' => 'User',
				'action' => 'add'
		));

		$this->add( '/user/read/@id', 'GET', array(
				'controller' => 'User',
				'action' => 'read'
		));

		$this->add( '/user/edit/@id', 'GET', array(
				'controller' => 'User',
				'action' => 'edit'
		));

		$this->add( '/user/update/@id', 'POST', array(
				'controller' => 'User',
				'action' => 'update'
		));

		$this->add( '/user/delete/@id', array('GET', 'DELETE'), array(
				'controller' => 'User',
				'action' => 'delete'
		));
		
        // groups list
		$this->add( '/users/groups', array('GET', 'POST'), array(
				'controller' => 'Groups',
				'action' => 'display'
		));

		$this->add( '/users/groups/page/@page', array('GET', 'POST'), array(
				'controller' => 'Groups',
				'action' => 'display'
		));
		
		$this->add( '/users/groups/delete', array('GET', 'POST'), array(
				'controller' => 'Groups',
				'action' => 'delete'
		));
		
        // groups crud
		$this->add( '/users/group/create', 'GET', array(
				'controller' => 'Group',
				'action' => 'create'
		));
		
		$this->add( '/users/group/add', 'POST', array(
				'controller' => 'Group',
				'action' => 'add'
		));

		$this->add( '/users/group/read/@id', 'GET', array(
				'controller' => 'Group',
				'action' => 'read'
		));
		
		$this->add( '/users/group/edit/@id', 'GET', array(
				'controller' => 'Group',
				'action' => 'edit'
		));
		
		$this->add( '/users/group/update/@id', 'POST', array(
				'controller' => 'Group',
				'action' => 'update'
		));
		
		$this->add( '/users/group/delete/@id', array('GET', 'DELETE'), array(
				'controller' => 'Group',
				'action' => 'delete'
		));
	}
}