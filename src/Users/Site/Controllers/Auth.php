<?php
namespace Users\Site\Controllers;

/**
 * Any private controllers -- controllers that require authentication 
 * in order to execute ANY AND ALL of their methods -- 
 * can extend this class.  
 * Alternatively, just check $this->getIdentity() yourself.
 *
 */
class Auth extends \Dsc\Controller
{
    public function beforeRoute($f3)
    {
        $identity = $this->getIdentity();
        if (empty($identity->id))
        {
            $f3->reroute('/login');
        }
    }
}