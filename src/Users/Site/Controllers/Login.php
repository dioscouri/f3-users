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
        
        $flash = \Dsc\Flash::instance();
        $f3->set('flash', $flash );
        
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
        $f3 = \Base::instance();
        
        $data = array(
        	'email' => trim( strtolower( $this->input->get( 'email', null, 'string' ) ) ),
            'username' => $this->input->get( 'username', null, 'string' ),
            'first_name' => $this->input->get( 'first_name', null, 'string' ),
            'last_name' => $this->input->get( 'last_name', null, 'string' ),
        );
        
        $user = (new \Users\Models\Users)->bind($data);
        
        // Check if the email already exists and give a custom message if so
        if (!empty($user->email) && $existing = $user->emailExists( $user->email ))
        {
            if ((empty($user->id) || $user->id != $existing->id))
            {
                // This email is already registered
                // Push the user back to the login page,
                // and tell them that they must first sign-in using another method (the one they previously setup),
                // then upon login, they can link this current social provider to their existing account
                \Dsc\System::addMessage( 'This email is already registered.', 'error' );
                
                \Dsc\System::instance()->setUserState('users.site.register.flash_filled', true);
                $flash = \Dsc\Flash::instance();
                $flash->store($user->cast());
                        
                $f3->reroute( '/register' );
        
                return;
            }
        }
        
        try
        {
            // this will handle other validations, such as username uniqueness, etc
            $user->save();
        }
        catch(\Exception $e)
        {
            \Dsc\System::addMessage( 'Save failed', 'error' );
            \Dsc\System::addMessage( $e->getMessage(), 'error' );
        
            \Dsc\System::instance()->setUserState('users.site.register.flash_filled', true);
            $flash = \Dsc\Flash::instance();
            $flash->store($user->cast());
        
            $f3->reroute('/register');
        
            return;
        }
        
        // if we have reached here, then all is right with the form
        $flash = \Dsc\Flash::instance();
        $flash->store(array());
        
        $settings = \Users\Models\Settings::fetch();
        $registration_action = $settings->{'general.registration.action'};
        switch ($registration_action)
        {
        	case "auto_login":
        	    $user->active = true;
        	    $user->save();
        	    
        	    \Dsc\System::instance()->get( 'auth' )->login( $user );
        	    
        	    $redirect = '/user';
        	    if ($custom_redirect = \Dsc\System::instance()->get( 'session' )->get( 'site.login.redirect' ))
        	    {
        	        $redirect = $custom_redirect;
        	    }
        	    \Dsc\System::instance()->get( 'session' )->set( 'site.login.redirect', null );
        	     
        	    break;
        	case "auto_login_with_validation":
        	    $user->active = false;
        	    $user->save();
        	            	    
        	    \Dsc\System::instance()->get( 'auth' )->login( $user );
        	    
        	    $redirect = '/user';
        	    if ($custom_redirect = \Dsc\System::instance()->get( 'session' )->get( 'site.login.redirect' ))
        	    {
        	        $redirect = $custom_redirect;
        	    }
        	    \Dsc\System::instance()->get( 'session' )->set( 'site.login.redirect', null );

        	    // Send validation email
        	    $user->sendEmailValidatingEmailAddress();
        	    
        	    break;
        	default:
        	    $user->active = false;
        	    $user->save();
        	            	    
        	    $redirect = '/login/validate';
        	    
        	    // Send validation email
                $user->sendEmailValidatingEmailAddress();
        	    
        	    break;
        }
        
        // redirect to the requested target, or the default if none requested
        \Base::instance()->reroute( $redirect );
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
                
                // redirect to the requested target, or the default if none requested
                $redirect = '/user';
                if ($custom_redirect = \Dsc\System::instance()->get( 'session' )->get( 'site.login.redirect' ))
                {
                    $redirect = $custom_redirect;
                }
                \Dsc\System::instance()->get( 'session' )->set( 'site.login.redirect', null );
                \Base::instance()->reroute( $redirect );
            }
            
            // here lets check if the user email we got from the provider already exists in our database ( for this example the email is UNIQUE for each user )
            // if authentication does not exist, but the email address returned by the provider does exist in database,
            // then authenticatewith the user having the address email in the database
            if ($user_profile->email)
            {
                // now check via email
                $user = (new \Users\Models\Users)->setState( 'filter.email', $user_profile->email )->getItem();
                if (!empty($user->id))
                {
                    $user->set( 'social.' . $provider . 'profile', (array) $adapter->getUserProfile() );
                    $user->save();
                    $this->auth->setIdentity( $user );
                    
                    // redirect to the requested target, or the default if none requested
                    $redirect = '/user';
                    if ($custom_redirect = \Dsc\System::instance()->get( 'session' )->get( 'site.login.redirect' ))
                    {
                        $redirect = $custom_redirect;
                    }
                    \Dsc\System::instance()->get( 'session' )->set( 'site.login.redirect', null );
                    \Base::instance()->reroute( $redirect );
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
            
            // 4 - if authentication does not exist and email is not in use, then we are creating a new user
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

        }
        catch ( \Exception $e )
        {
            $user_error = null;
            
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
            	    $user_error = "Authentication failed.";
            	    break;
            	case 6 :
            	    $error = "User profile request failed. Most likely the user is not connected to the provider and he should to authenticate again.";
            	    $user_error = "We were unable to get your profile.  Please authenticate again with the profile provider.";
            	    $adapter->logout();
            	    break;
            	case 7 :
            	    $error = "User not connected to the provider.";
            	    $user_error = "No profile found with the provider.  Missing connection.";
            	    $adapter->logout();
            	    break;
            }
                        
            if ($f3->get( 'DEBUG' ))
            {
                // if debug mode is enabled, display the full error
                $error .= "<br /><br /><b>Original error message:</b> " . $e->getMessage();
                $error .= "<hr /><pre>Trace:<br />" . $e->getTraceAsString() . "</pre>";
            }
            else
            {
                // otherwise, display something simple
                $error = $user_error;
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

        $username = $this->input->get( 'username', null, 'string' );
        $user->username = $username;
        
        // Check if the email already exists and give a custom message if so
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
            // this will handle other validations, such as username uniqueness, etc
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

        // redirect to the requested target, or the default if none requested
        $redirect = '/user';
        if ($custom_redirect = \Dsc\System::instance()->get( 'session' )->get( 'site.login.redirect' ))
        {
            $redirect = $custom_redirect;
        }
        \Dsc\System::instance()->get( 'session' )->set( 'site.login.redirect', null );
        \Base::instance()->reroute( $redirect );
    }
    
    /**
     * Display a static page requesting email validation
     */
    public function validate()
    {
        $view = \Dsc\System::instance()->get( 'theme' );
        echo $view->render( 'Users/Site/Views::login/validate.php' );
    }
    
    /**
     * Validates a token, usually from clicking on a link in an email
     * 
     * @throws \Exception
     */
    public function validateToken()
    {
        $f3 = \Base::instance();
        $token = $this->inputfilter->clean( $f3->get('PARAMS.token'), 'alnum' );
        
        try {
            
            $user = (new \Users\Models\Users)->setState('filter.id', $token)->getItem();
            if (empty($user->id) || $token != (string) $user->id) 
            {
            	throw new \Exception( 'Invalid Token' );
            }
            $user->active = true;
            $user->save();
            
        } catch (\Exception $e) {

            \Dsc\System::addMessage( 'Email validation failed.  Please confirm the token and try again.', 'error' );
            \Dsc\System::addMessage( $e->getMessage(), 'error' );
            \Base::instance()->reroute( '/login/validate' );
            return;            
        }

        // redirect to login
        \Dsc\System::addMessage( 'Thank you for validating your email address. You may now login.' );
        \Base::instance()->reroute( '/login' );
    }
}
