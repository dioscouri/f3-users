<?php

namespace Users\Site;

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
					'namespace' => '\Users\Site\Controllers',
					'url_prefix' => '/'
				)
		);
		
		$this->add( '/signup', 'GET', array(
							'controller' => 'Auth',
							'action' => 'showSignup'
							));
		
		$this->add( '/login', 'GET', array(
							'controller' => 'Auth',
							'action' => 'showshowLogin'
							));

		$this->add( '/signup', 'POST', array(
				'controller' => 'Auth',
				'action' => 'doSignup'
		));

		$this->add( '/login', 'POST', array(
				'controller' => 'Auth',
				'action' => 'doLogin'
		));

		$this->add( '/logout', array('GET', 'POST'), array(
				'controller' => 'User',
				'action' => 'logout'
		));
	}
}