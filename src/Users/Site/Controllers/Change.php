<?php
namespace Users\Site\Controllers;

class Change extends \Users\Site\Controllers\Auth
{
    public function password()
    {
        $this->app->set('meta.title', 'Change Password');
        
        $view = \Dsc\System::instance()->get( 'theme' );
        echo $view->render( 'Users/Site/Views::change/password.php' );
    }
    
    public function avatar()
    {
    	$this->app->set('meta.title', 'Change Avatar');
    
    	$view = \Dsc\System::instance()->get( 'theme' );
    	echo $view->render( 'Users/Site/Views::change/avatar.php' );
    }
    
    public function avatarSubmit()
    {	
    	$user = $this->getIdentity();
    	//TODO Should we delete the previous avatar?
    	if(!empty($_FILES['avatar'])) {
    		//todo more width/height to settings
    		$_FILES['avatar']['name'] = $user->fullName() . "'s Avatar";
    		$avatar = \Users\Models\Avatars::createFromUpload($_FILES['avatar'], array('width'=>200, 'height'=>200, 'tags' =>array($user->id, $user->fullName()) ));
    		$user->set('avatar.slug', $avatar->{'slug'});
    		$user->save();
    	}

    	//RENDER THE PAGE AGAIN
    	$this->app->set('meta.title', 'Change Avatar');
    	$view = \Dsc\System::instance()->get( 'theme' );
    	echo $view->render( 'Users/Site/Views::change/avatar.php' );
    }

    public function passwordSubmit()
    {
        $f3 = \Base::instance();
        
        $data = array(
            'new_password' => $this->input->get( 'new_password', null, 'string' ),
            'confirm_new_password' => $this->input->get( 'confirm_new_password', null, 'string' )
        );
        
        $user = $this->getIdentity();
        $user->bind($data);
        
        try
        {
            $user->save()->sendEmailPasswordResetNotification();
        }
        catch(\Exception $e)
        {
            \Dsc\System::addMessage( 'Password update failed.', 'error' );
            \Dsc\System::addMessage( $e->getMessage(), 'error' );

            $f3->reroute('/user/change-password');        
            return;
        }
        
        \Dsc\System::addMessage( 'Password has been updated.' );
        
        $redirect = '/user';
        if ($custom_redirect = \Dsc\System::instance()->get( 'session' )->get( 'change_password.redirect' ))
        {
            $redirect = $custom_redirect;
        }
        
        \Dsc\System::instance()->get( 'session' )->set( 'change_password.redirect', null );
        $f3->reroute( $redirect );
    }
    
    public function email()
    {
        $this->app->set('meta.title', 'Change Email Address');
        
        $view = \Dsc\System::instance()->get( 'theme' );
        echo $view->render( 'Users/Site/Views::change/email.php' );
    }
    
    public function emailSubmit()
    {
        $f3 = \Base::instance();
    
        $email = $this->input->get( 'new_email', null, 'string' );
        
        $data = array(
            'change_email' => array(
                'email' => $email,
                'token' => (string) new \MongoId,
                'created' => \Dsc\Mongo\Metastamp::getDate('now')
            )
        );
        
        // TODO Validate that it is an email        
        if (empty($email))
        {
            \Dsc\System::addMessage( 'Invalid Email.', 'error' );
            
            $f3->reroute('/user/change-email');
            return;
        }
        
        // Verify whether or not the email address is already registered
        if (\Users\Models\Users::emailExists( $email )) 
        {
            \Dsc\System::addMessage( 'Email address already registered.', 'error' );
            
            $f3->reroute('/user/change-email');
            return;        	
        }
    
        $user = $this->getIdentity();
        $user->bind($data);
    
        try
        {
            $user->save()->sendEmailChangeEmailConfirmation();
        }
        catch(\Exception $e)
        {
            \Dsc\System::addMessage( 'Email submission failed.', 'error' );
            \Dsc\System::addMessage( $e->getMessage(), 'error' );
    
            $f3->reroute('/user/change-email');
            return;
        }
    
        \Dsc\System::addMessage( 'Email change request submitted.  Please check your inbox for a verification email from us.' );
    
        $f3->reroute('/user/change-email/verify');
    }
    
    public function emailVerify()
    {
        $this->app->set('meta.title', 'Verify Email Address');
        
        $view = \Dsc\System::instance()->get( 'theme' );
        echo $view->render( 'Users/Site/Views::change/email_verify.php' );    
    }
    
    public function emailConfirm()
    {
        // new_email and token can be submitted via GET, such as in a link in an email
        $new_email = urldecode( $this->input->get( 'new_email', null, 'string' ) );
        $token = $this->input->get( 'token', null, 'string' );
        
        try {

            // If either value is empty, this fails
            if (empty($new_email) || empty($token))
            {
                throw new \Exception('Invalid inputs');     
            }
            
            $user = (new \Users\Models\Users)->setState('filter.new_email', $new_email)->setState('filter.new_email_token', $token)->getItem();
            if (empty($user->id) || $token != (string) $user->{'change_email.token'})
            {
                throw new \Exception( 'Token does not exist' );
            }
        
            // check the date when the token was created
            if ($user->{'change_email.created.time'} < strtotime('-2 hours'))
            {
                throw new \Exception( 'Token has expired' );
            }
        
            // ok, token is valid, so lets change it
            $user->email = $user->{'change_email.email'};
            $user->change_email = array();
            $user->save();
            
            // is the user logged in?  if so, update the identity
            $identity = $this->getIdentity();
            if (!empty($identity->id) && (string) $identity->id == (string) $user->id) 
            {
            	\Dsc\System::instance()->get('auth')->setIdentity( $user );
            }
        
        } catch (\Exception $e) {
        
            \Dsc\System::addMessage( 'Change email request has failed.', 'error' );
            \Dsc\System::addMessage( $e->getMessage(), 'error' );
            \Base::instance()->reroute( '/user/change-email' );
            return;
        }
        
        \Dsc\System::addMessage( 'Email changed.' );
        \Base::instance()->reroute( '/user' );
    }
    
    public function basicInfo()
    {
        $identity = $this->getIdentity();
        if ( empty( $identity->id ))
        {
            $this->app->reroute( '/user' );
        }
        
    	$this->app->set('meta.title', 'Change Basic Information');
    	
    	$view = \Dsc\System::instance()->get( 'theme' );
    	$this->app->set( 'identity', $identity );
    	echo $view->render( 'Users/Site/Views::change/basic.php' );
    }
    
    public function basicInfoSubmit()
    {
    	$identity = $this->getIdentity();
    	if ( empty( $identity->id ))
    	{
    		$this->app->reroute( '/user' );
    	}
    	$first_name = $this->input->get( 'first_name', null, 'string' );
    	$last_name = $this->input->get( 'last_name', null, 'string' );
    	$username = $this->input->get( 'username', null, 'alnum' );
    	 
    	
    	try
    	{
    	    if( empty( $username ) ) {
    			throw new \Exception( 'Your username is required' );
    		}
    		
    		if( empty( $first_name ) ) {
    			throw new \Exception( 'Your first name is required' );
    		}
    		
    		if( empty( $last_name ) ) {
    			throw new \Exception( 'Your last name is required' );
    		}
    		
    		$user = (new \Users\Models\Users)->setState('filter.username', $username)->getItem();
    		if (!empty($user->id) && ((string)$user->id != (string)$identity->id) )
    		{
    			throw new \Exception( 'This username is already taken' );
    		}
    		
    		$identity->first_name = $first_name;
    		$identity->last_name = $last_name;
    		$identity->username = $username;
    		$identity->save();    	
    	} catch( \Exception $e ) {
            
    		\Dsc\System::addMessage( 'Change basic information has failed.', 'error' );
            \Dsc\System::addMessage( $e->getMessage(), 'error' );
            \Base::instance()->reroute( '/user/change-basic' );
    		return;
    	}
        \Dsc\System::addMessage( 'Basic information changed.' );
        \Base::instance()->reroute( '/user' );
    }
}
