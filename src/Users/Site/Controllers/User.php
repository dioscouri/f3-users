<?php
namespace Users\Site\Controllers;

class User extends Auth
{
    public function read()
    {
        $f3 = \Base::instance();
        
        $user = $this->getItem();
        $f3->set('user', $user);
        
        $view = \Dsc\System::instance()->get('theme');
        echo $view->render( 'Users/Site/Views::profile/read.php' );
    }
    
    public function readSelf()
    {
        $f3 = \Base::instance();
    
        $identity = $this->getIdentity();
        if (empty($identity->id)) 
        {
            $f3->reroute( '/login' );
            return;
        }
        
        if (!empty($identity->__safemode)) 
        {
        	$user = $identity;
        } 
            else 
        {
            $model = $this->getModel()->setState( 'filter.id', $identity->id );
            $user = $model->getItem();
        }

        $f3->set('user', $user);
            
        $view = \Dsc\System::instance()->get('theme');
        echo $view->render( 'Users/Site/Views::profile/readSelf.php' );
    }
    
    protected function getModel()
    {
        $model = new \Users\Models\Users;
        return $model;
    }
    
    protected function getItem()
    {
        $f3 = \Base::instance();
        $id = $this->inputfilter->clean( $f3->get( 'PARAMS.id' ), 'alnum' );
        $model = $this->getModel()->setState( 'filter.id', $id );
    
        try
        {
            $item = $model->getItem();
        }
        catch ( \Exception $e )
        {
            \Dsc\System::instance()->addMessage( "Invalid Item: " . $e->getMessage(), 'error' );
            $f3->reroute( '/' );
            return;
        }
    
        return $item;
    }
    
    /**
     * Displays the logged-in user's list of linked social profiles
     */
    public function socialProfiles()
    {
        $f3 = \Base::instance();

        $settings = \Users\Models\Settings::fetch();
        if (!$settings->isSocialLoginEnabled())
        {
            \Base::instance()->reroute( "/user" );
        }
        
        $identity = $this->getIdentity();
        if (empty($identity->id))
        {
            $f3->reroute( '/login' );
            return;
        }
        
        if (!empty($identity->__safemode))
        {
            $f3->reroute( '/user' );
            return;
        }
        
        $user = $identity;
        $f3->set('user', $user);
        
        $view = \Dsc\System::instance()->get('theme');
        echo $view->render( 'Users/Site/Views::social/profiles.php' );        
    }
    
    public function unlinkSocialProfile()
    {
        $settings = \Users\Models\Settings::fetch();
        if (!$settings->isSocialLoginEnabled())
        {
            \Base::instance()->reroute( "/user" );
        }
                
    	$f3 = \Base::instance();
    	$provider = strtolower( $this->inputfilter->clean( $f3->get( 'PARAMS.provider' ), 'alnum' ) );
    	 
    	$identity = $this->getIdentity();
    	if (empty($identity->id))
    	{
    		$f3->reroute( '/login' );
    		return;
    	}
    	
    	if (!empty($identity->__safemode))
    	{
    	    $f3->reroute( '/user' );
    	    return;
    	}
    	
    	$user = $identity;
    	
    	try {
    	    $user->clear( 'social.'.$provider );
    	    $user->save();
    	    \Dsc\System::addMessage( 'Profile unlinked.', 'success' );
    	}
    	catch(\Exception $e) {
    	    \Dsc\System::addMessage( 'Could not unlink profile.', 'error' );
    	    \Dsc\System::addMessage( $e->getMessage(), 'error' );
    	}
    	
    	$f3->reroute( '/user/social-profiles' );
    	return; 
    }
    
    public function linkSocialProfile()
    {
        $settings = \Users\Models\Settings::fetch();
        if (!$settings->isSocialLoginEnabled())
        {
            \Base::instance()->reroute( "/user" );
        }
                
        $f3 = \Base::instance();
        $provider = $f3->get( 'PARAMS.provider' );
        $hybridauth_config = \Users\Models\Settings::fetch();
        $config = (array) $hybridauth_config->{'social'};
        
        // set custom endpoint for linking on existing users
        $config['base_url'] = $f3->get('SCHEME') . '://' . $f3->get('HOST') . $f3->get('BASE') . '/user/social/link';
        
        try
        {
            // create an instance for Hybridauth with the configuration file path as parameter
            $hybridauth = new \Hybrid_Auth( $config );
        
            // try to authenticate the selected $provider
            $adapter = $hybridauth->authenticate( $provider );
        
            // grab the user profile
            $user_profile = $adapter->getUserProfile();
        
            // 1 - try to lookup the user based on the profile.identifier
            // if found, log them in to our system and redirect to their profile page
            $model = new \Users\Models\Users;
            $filter = 'social.' . $provider . '.profile.identifier';
            $user = $model->setCondition( $filter, $user_profile->identifier )->getItem();
            if (! empty( $user->id ))
            {
                \Dsc\System::instance()->get( 'auth' )->login( $user );
        
                // redirect to the requested target, or the default if none requested
                $redirect = '/user';
                if ($custom_redirect = \Dsc\System::instance()->get( 'session' )->get( 'site.login.redirect' ))
                {
                    $redirect = $custom_redirect;
                }
                \Dsc\System::instance()->get( 'session' )->set( 'site.login.redirect', null );
                \Base::instance()->reroute( $redirect );
            }
        
            // 2 - check if the user email we got from the provider already exists in our database ( for this example the email is UNIQUE for each user )
            if ($user_profile->email)
            {
                // 3 - if the email address returned by the provider does exist in our database,
                // then authenticate with that user
                $user = (new \Users\Models\Users)->setState( 'filter.email', $user_profile->email )->getItem();
                if (!empty($user->id))
                {
                    $user->set( 'social.' . $provider . '.profile', (array) $adapter->getUserProfile() );
                    $user->save();
        
                    \Dsc\System::instance()->get( 'auth' )->login( $user );
        
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
        
            // 4 - if social profile id does not exist in our database and email is not in use, then we are creating a new user
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
    
    public function linkSocialProfileEndpoint()
    {
        try
        {
            \Hybrid_Endpoint::process();
        }
        catch ( \Exception $e )
        {
            \Dsc\System::addMessage( 'Linking failed.', 'error' );
            \Dsc\System::addMessage( $e->getMessage(), 'error' );
            \Base::instance()->reroute( "/user/social-profiles" );
        }
    }
}
