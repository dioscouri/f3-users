<?php
namespace Users\Site\Controllers;

/**
 * Any private controllers -- controllers that require authentication 
 * in order to execute ANY AND ALL of their methods -- 
 * can extend this class.  
 * 
 * Alternatively, just run $this->requireIdentity() inside your restricted methods.
 *
 */
class Auth extends \Dsc\Controller
{
    public function beforeRoute()
    {
        $this->requireIdentity();
    }
}
