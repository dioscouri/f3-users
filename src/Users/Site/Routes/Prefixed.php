<?php
namespace Users\Site\Routes;

/**
 * Group class is used to keep track of a group of routes with similar aspects (the same controller, the same f3-app and etc)
 */
class Prefixed extends \Dsc\Routes\Group
{

    /**
     * Initializes all routes for this group
     * NOTE: This method should be overriden by every group
     */
    public function initialize()
    {
        $this->setDefaults( array(
            'namespace' => '\Users\Site\Controllers',
            'url_prefix' => '/user' 
        ) );
        
        $this->add( '', 'GET', array(
            'controller' => 'User',
            'action' => 'readSelf' 
        ) );
        
        $this->add( '/@id', 'GET', array(
            'controller' => 'User',
            'action' => 'read' 
        ) );
    }
}