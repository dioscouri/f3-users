<?php
namespace Users\Site;

class Routes extends \Dsc\Routes\Group
{

    public function initialize()
    {
        $f3 = \Base::instance();
        
        $this->setDefaults(array(
            'namespace' => '\Users\Site\Controllers',
            'url_prefix' => ''
        ));
        
        $this->add('/login', 'GET', array(
            'controller' => 'Login',
            'action' => 'index'
        ));
        
        $this->add('/sign-in', 'GET', array(
            'controller' => 'Login',
            'action' => 'only'
        ));
        
        $this->add('/login', 'POST', array(
            'controller' => 'Login',
            'action' => 'auth'
        ));
        
        $this->add('/logout', 'GET|POST', array(
            'controller' => 'Login',
            'action' => 'logout'
        ));
        
        $this->add('/register', 'GET', array(
            'controller' => 'Login',
            'action' => 'register'
        ));
        
        $this->add('/register', 'POST', array(
            'controller' => 'Login',
            'action' => 'create'
        ));
        
        $this->add('/login/social', 'GET|POST', array(
            'controller' => 'Login',
            'action' => 'social'
        ));
        
        $this->add('/login/social/auth/@provider', 'GET|POST', array(
            'controller' => 'Login',
            'action' => 'provider'
        ));
        
        $this->add('/login/completeProfile', 'GET', array(
            'controller' => 'Login',
            'action' => 'completeProfileForm'
        ));
        
        $this->add('/login/completeProfile', 'POST', array(
            'controller' => 'Login',
            'action' => 'completeProfile'
        ));
        
        $this->add('/login/validate', 'GET', array(
            'controller' => 'Login',
            'action' => 'validate'
        ));
        
        $f3->route('POST /login/validate', function ($f3)
        {
            $token = $f3->get('REQUEST.token');
            $f3->reroute('/login/validate/token/' . $token);
        });
        
        $this->add('/login/validate/token/@token', 'GET', array(
            'controller' => 'Login',
            'action' => 'validateToken'
        ));
        
        $this->add('/login/validate-email', 'GET', array(
            'controller' => 'Login',
            'action' => 'validateEmail'
        ));

        $this->add('/login/validate-email', 'POST', array(
            'controller' => 'Login',
            'action' => 'validateEmailSubmit'
        ));
    }
}