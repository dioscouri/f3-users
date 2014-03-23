<?php 
namespace Users\Site\Controllers;

class Login extends \Dsc\Controller 
{
    /**
     * Displays a dual login/register form
     */
    public function index($f3)
    {
        $identity = $this->getIdentity();
        if (!empty($identity->id))
        {
            $f3->reroute('/user');
        }
        
        $view = \Dsc\System::instance()->get('theme');
        echo $view->render( 'Users/Site/Views::login/dual.php' );        
    }
    
    /**
     * Displays just a login form
     */
    public function only($f3)
    {
        $identity = $this->getIdentity();
        if (!empty($identity->id))
        {
            $f3->reroute('/user');
        }
    
        $view = \Dsc\System::instance()->get('theme');
        echo $view->render( 'Users/Site/Views::login/login.php' );
    }
    
    /**
     * Displays just a registration form
     */
    public function register($f3)
    {
        $identity = $this->getIdentity();
        if (!empty($identity->id))
        {
            $f3->reroute('/user');
        }
    
        $view = \Dsc\System::instance()->get('theme');
        echo $view->render( 'Users/Site/Views::login/register.php' );
    }
    
    /**
     * Performs logout
     */
    public function logout()
    {
        \Base::instance()->clear('SESSION');
        \Base::instance()->reroute('/');
    }
    
    /**
     * Authenticates the user (performs the login)
     */
    public function auth()
    {
        $username_input = $this->input->getAlnum('login-username');
        $password_input = $this->input->getString('login-password');
        
        if (empty($username_input) || empty($password_input)) 
        {
            \Dsc\System::instance()->addMessage('Login failed - Incomplete Form', 'error');
            \Base::instance()->reroute("/login");
            return;
        }
        
        // TODO Push this to the \Users\Lib\Auth class, and let it run through any Auth listeners
        $input = $this->input->getArray();
        
        try {
            
            $this->auth->check($input);
            \Base::instance()->reroute("/user");
            return;
            
        } catch (\Exception $e) {
            \Dsc\System::addMessage('Login failed', 'error');
            \Dsc\System::addMessage($e->getMessage(), 'error');
            \Base::instance()->reroute("/login");
            return;
        }

        \Base::instance()->reroute("/");
        return;            
    }
    
    /**
     * Creates the user
     * (target for the register form)
     */
    public function create()
    {
        
    }

}
?> 