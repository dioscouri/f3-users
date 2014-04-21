<?php
namespace Users\Site\Controllers;

class Login extends \Dsc\Controller
{

    /**
     * Displays a dual login/register form
     */
    public function index( $f3 )
    {
        $identity = $this->getIdentity();
        if (! empty( $identity->id ))
        {
            $f3->reroute( '/user' );
        }
        
        $view = \Dsc\System::instance()->get( 'theme' );
        echo $view->render( 'Users/Site/Views::login/dual.php' );
    }

    /**
     * Displays just a login form
     */
    public function only( $f3 )
    {
        $identity = $this->getIdentity();
        if (! empty( $identity->id ))
        {
            $f3->reroute( '/user' );
        }
        
        $view = \Dsc\System::instance()->get( 'theme' );
        echo $view->render( 'Users/Site/Views::login/login.php' );
    }

    /**
     * Displays just a registration form
     */
    public function register( $f3 )
    {
        $identity = $this->getIdentity();
        if (! empty( $identity->id ))
        {
            $f3->reroute( '/user' );
        }
        
        $view = \Dsc\System::instance()->get( 'theme' );
        echo $view->render( 'Users/Site/Views::login/register.php' );
    }

    /**
     * Performs logout
     */
    public function logout()
    {
        \Dsc\System::instance()->get( 'auth' )->logout();
        \Base::instance()->reroute( '/' );
    }

    /**
     * Authenticates the user (performs the login)
     */
    public function auth()
    {
        /*
         * Let $this->auth->check() set the error, in case we want to pass social logins through this auth method $username_input = $this->input->getAlnum('login-username'); $password_input = $this->input->getString('login-password'); if (empty($username_input) || empty($password_input)) { \Dsc\System::instance()->addMessage('Login failed - Incomplete Form', 'error'); \Base::instance()->reroute("/login"); return; }
         */
        $redirect = '/user';
        if ($custom_redirect = \Dsc\System::instance()->get( 'session' )->get( 'site.login.redirect' ))
        {
            $redirect = $custom_redirect;
        }
        
        $input = $this->input->getArray();
        
        try
        {
            $this->auth->check( $input );
        }
        catch ( \Exception $e )
        {
            \Dsc\System::addMessage( 'Login failed', 'error' );
            \Dsc\System::addMessage( $e->getMessage(), 'error' );
            \Base::instance()->reroute( "/login" );
            return;
        }
        
        \Dsc\System::instance()->get( 'session' )->set( 'site.login.redirect', null );
        \Base::instance()->reroute( $redirect );
        
        return;
    }

    /**
     * Creates the user
     * (target for the register form)
     */
    public function create()
    {
    }

    /**
     * Target for social logins
     */
    public function social()
    {
        try
        {
            \Hybrid_Endpoint::process();
        }
        catch ( \Exception $e )
        {
            \Dsc\System::addMessage( 'Login failed', 'error' );
            \Dsc\System::addMessage( $e->getMessage(), 'error' );
            \Base::instance()->reroute( "/login" );
        }
    }

    /**
     * 
     */
    public function provider()
    {
        $f3 = \Base::instance();
        $provider = $f3->get( 'PARAMS.provider' );
        $hybridauth_config = \Users\Models\Settings::fetch();
        $config = (array) $hybridauth_config->{'social'};
        
        if (empty($config['base_url'])) {
            $config['base_url'] = $f3->get('SCHEME') . '://' . $f3->get('HOST') . $f3->get('BASE') . '/login/social';
        }
        
        try
        {
            // create an instance for Hybridauth with the configuration file path as parameter
            $hybridauth = new \Hybrid_Auth( $config );
            
            // try to authenticate the selected $provider
            $adapter = $hybridauth->authenticate( $provider );
            
            // grab the user profile
            $user_profile = $adapter->getUserProfile();
            
            // first try to lookup the user based on the profile.identifier
            // if found, log them in to our system and redirect to their profile page
            $model = new \Users\Models\Users;
            $filter = 'social.' . $provider . '.profile.identifier';            
            $user = $model->setCondition( $filter, $user_profile->identifier )->getItem();            
            if (! empty( $user->id ))
            {
                $this->auth->setIdentity( $user );
                $f3->reroute( '/user' );
            }
            
            // here lets check if the user email we got from the provider already exists in our database ( for this example the email is UNIQUE for each user )
            // if authentication does not exist, but the email address returned by the provider does exist in database,
            // then authenticatewith the user having the address email in the database
            if ($user_profile->email)
            {
                // now check via email
                $model = new \Users\Models\Users();
                $model->setState( 'filter.email', $user_profile->email );
                $user = $model->getItem();
                if (!empty($user->id))
                {
                    $user->set( 'social.' . $provider . 'profile', (array) $adapter->getUserProfile() );
                    $user->save();
                    $this->auth->setIdentity( $user );
                    $f3->reroute( '/user' );
                }
                
                // email doesn't exist in our database 
                else 
                {
                	
                }
            }
            
            // email not provided by provider
            else 
            {
            	
            }
            
            // 4 - if authentication does not exist and email is not in use, then we create a new user
            // so first let's prepare the data 
            $data = array();
            $data['social'][$provider]['profile'] = (array) $adapter->getUserProfile();
            $data['social'][$provider]['access_token'] = (array) $adapter->getAccessToken();
            $data['email'] = $user_profile->email;
            $data['first_name'] = $user_profile->firstName;
            $data['last_name'] = $user_profile->lastName;
            $data['username'] = \Users\Models\Users::usernameFromString( $user_profile->displayName );
            
            // if last name is empty, try to extract last name from first name field
            if (empty($user_profile->lastName) && !empty($user_profile->firstName) && strrpos($user_profile->firstName, ' ') !== false ) 
            {
            	$pieces = explode(' ', $user_profile->firstName, 2);
            	$data['first_name'] = $pieces[0];
            	$data['last_name'] = $pieces[1];            	
            }
            
            // put the data array into the session, and bind the array to a Users\Models\Users object on the flip side
            \Dsc\System::instance()->get('session')->set('users.incomplete_provider_data', $data );
            
            // Now push the user to a "complete your profile" form prepopulated with data from the provider identity
            $f3->reroute( '/login/completeProfile' );
            
            // 4.1 - create new user
            /**
            $model = new \Users\Models\Users();
            $user = $model->create( $data );
            $this->auth->setIdentity( $user );
            $f3->reroute( '/user' );
             */
        }
        catch ( \Exception $e )
        {
            // Display the recived error
            if ($f3->get( 'DEBUG' ))
            {
                
                switch ($e->getCode())
                {
                    case 0 :
                        $error = "Unspecified error.";
                        break;
                    case 1 :
                        $error = "Hybridauth configuration error.";
                        break;
                    case 2 :
                        $error = "Provider not properly configured.";
                        break;
                    case 3 :
                        $error = "Unknown or disabled provider.";
                        break;
                    case 4 :
                        $error = "Missing provider application credentials.";
                        break;
                    case 5 :
                        $error = "Authentication failed. The user has canceled the authentication or the provider refused the connection.";
                        break;
                    case 6 :
                        $error = "User profile request failed. Most likely the user is not connected to the provider and he should to authenticate again.";
                        $adapter->logout();
                        break;
                    case 7 :
                        $error = "User not connected to the provider.";
                        $adapter->logout();
                        break;
                }
                
                // well, basically your should not display this to the end user, just give him a hint and move on..
                $error .= "<br /><br /><b>Original error message:</b> " . $e->getMessage();
                $error .= "<hr /><pre>Trace:<br />" . $e->getTraceAsString() . "</pre>";
            }
            else
            {
                
                $error = 'Something went wrong';
            }
            
            \Dsc\System::addMessage( 'Login failed', 'error' );
            \Dsc\System::addMessage( $error, 'error' );
            
            $f3->reroute( '/login' );
        }
    }

    /**
     * Displays a profile completion form
     */
    public function completeProfileForm()
    {
        $f3 = \Base::instance();
        
        $identity = $this->getIdentity();
        if (! empty( $identity->id ))
        {
            $f3->reroute( '/user' );
        }
        
        // bind the data to a model
        $data = \Dsc\System::instance()->get('session')->get('users.incomplete_provider_data' );
        $user = (new \Users\Models\Users)->bind($data);
        
        $flash = \Dsc\Flash::instance();
        $f3->set('flash', $flash );
        
        $flash_filled = \Dsc\System::instance()->getUserState('users.site.login.complete_profile.flash_filled');
        if (!$flash_filled) {
            $flash->store($user->cast());
        }
        
        // TODO If the profile is complete, redirect to /user
        \Base::instance()->set('model', $user);
    
        $view = \Dsc\System::instance()->get( 'theme' );
        echo $view->renderTheme( 'Users/Site/Views::login/complete_profile.php' );
    }
    
    /**
     * Target for the completeProfileForm submission 
     */
    public function completeProfile()
    {
        $f3 = \Base::instance();
        
        $data = \Dsc\System::instance()->get('session')->get('users.incomplete_provider_data' );
        $user = (new \Users\Models\Users)->bind($data);
         
        $email = $this->input->get( 'email', null, 'string' );
        $user->email = $email;
        
        // Check if the email already exists
        if (!empty($user->email) && $existing = $user->emailExists( $user->email ))
        {
            if ((empty($user->id) || $user->id != $existing->id))
            {
                // This email is already registered
                // Push the user back to the login page,
                // and tell them that they must first sign-in using another method (the one they previously setup),
                // then upon login, they can link this current social provider to their existing account
                \Dsc\System::addMessage( 'This email is already registered.', 'error' );
                \Dsc\System::addMessage( 'Please login using the registered email address or with the other social profile that also uses this email address.', 'error' );
                \Dsc\System::addMessage( 'Once you are logged in, you may link additional social profiles to your account.', 'error' );
                
                $f3->reroute( '/login' );
                
                return;
            }
        }
        
        try 
        {
            $user->save();
        } 
        catch(\Exception $e) 
        {
            \Dsc\System::addMessage( 'Save failed', 'error' );
            \Dsc\System::addMessage( $e->getMessage(), 'error' );

            \Dsc\System::instance()->setUserState('users.site.login.complete_profile.flash_filled', true);
            $flash = \Dsc\Flash::instance();
            $flash->store($user->cast());
            
            $f3->reroute('/login/completeProfile');
            
            return;
        }

        // if we have reached here, then all is right with the world.  login the user.
        $this->auth->setIdentity( $user );        
        \Dsc\System::instance()->get('session')->set('users.incomplete_provider_data', array() );        
        $f3->reroute( '/user' );
    }
}
