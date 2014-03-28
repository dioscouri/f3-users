<?php
namespace Users\Site;

/**
 * Group class is used to keep track of a group of routes with similar aspects (the same controller, the same f3-app and etc)
 */
class Routes extends \Dsc\Routes\Group
{

    /**
     * Initializes all routes for this group
     * NOTE: This method should be overriden by every group
     */
    public function initialize()
    {
        $this->setDefaults( array(
            'namespace' => '\Users\Site\Controllers',
            'url_prefix' => '' 
        ) );
        
        $this->add( '/login', 'GET', array(
            'controller' => 'Login',
            'action' => 'index' 
        ) );
        
        $this->add( '/sign-in', 'GET', array(
            'controller' => 'Login',
            'action' => 'only' 
        ) );
        
        $this->add( '/login', 'POST', array(
            'controller' => 'Login',
            'action' => 'auth' 
        ) );
        
        $this->add( '/logout', 'GET|POST', array(
            'controller' => 'Login',
            'action' => 'logout' 
        ) );
        
        $this->add( '/register', 'GET', array(
            'controller' => 'Login',
            'action' => 'register' 
        ) );
        
        $this->add( '/register', 'POST', array(
            'controller' => 'Login',
            'action' => 'create' 
        ) );
        
        $this->add( '/login/social', 'GET|POST', array(
            'controller' => 'Login',
            'action' => 'social' 
        ) );

        $this->add( '/login/social/auth/@provider', 'GET|POST', array(
            'controller' => 'Login',
            'action' => 'provider' 
        ) ); 
    }
}