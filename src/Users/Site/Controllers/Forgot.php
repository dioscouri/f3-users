<?php
namespace Users\Site\Controllers;

class Forgot extends \Dsc\Controller
{
    /**
     * Step 1 == Page that begins the forgotten password process.
     */
    public function password()
    {
        $this->app->set('meta.title', 'Forgot Password');
        
        $view = \Dsc\System::instance()->get( 'theme' );
        echo $view->render( 'Users/Site/Views::forgot/password_email_form.php' );
    }
    
    /**
     * Step 2 == POST target for the forgot password form.
     * Looks up the provided email address, and if it's found, sends an email and displays a static message.
     * If it's not found, queues a message and reroutes to the forgot-password route
     */
    public function passwordFindEmail($f3)
    {
        $email = trim( strtolower( $this->input->get('email', null, 'string') ) );
        
        $user = (new \Users\Models\Users)->setState('filter.email', $email)->getItem();
        if (empty($user->id)) 
        {
            \Dsc\System::addMessage( 'That email address does not exist in our system.', 'error' );
            $f3->reroute('/user/forgot-password');
            return;
        }
        
        // create a forgot_password array, with a MongoId and a metastamp
        $user->forgot_password = array(
        	'token' => (string) new \MongoId,
            'created' => \Dsc\Mongo\Metastamp::getDate('now')
        );
        
        $user->save()->sendEmailResetPassword();
        
        \Dsc\System::addMessage( 'Email sent' );
        
        $f3->reroute('/user/forgot-password/email');
    }
    
    /**
     * Step 3 == Tell the user to check their email 
     */
    public function passwordEmailSent()
    {
        $this->app->set('meta.title', 'Email Sent | Forgot Password');
        
        $view = \Dsc\System::instance()->get( 'theme' );
        echo $view->render( 'Users/Site/Views::forgot/password_email_sent.php' );
    }
    
    /**
     * Step 4 == Displays the password reset form if the token is valid
     */
    public function passwordReset()
    {
        $f3 = \Base::instance();
        $token = $this->inputfilter->clean( $f3->get('PARAMS.token'), 'alnum' );
        
        try {
        
            $user = (new \Users\Models\Users)->setState('filter.forgot_password.token', $token)->getItem();
            if (empty($user->id) || $token != (string) $user->{'forgot_password.token'})
            {
                throw new \Exception( 'Token does not exist' );
            }
            
            // check the date when the token was created
            if ($user->{'forgot_password.created.time'} < strtotime('-2 hours')) 
            {
                throw new \Exception( 'Token has expired' );
            }

            // ok, token is valid, so lets delete it
            $user->forgot_password = array();
            $user->save();
            
        } catch (\Exception $e) {
        
            \Dsc\System::addMessage( 'Password reset request has failed.', 'error' );
            \Dsc\System::addMessage( $e->getMessage(), 'error' );
            \Base::instance()->reroute( '/user/forgot-password' );
            return;
        }
        
        // store the user->id in the session so we know who we're updating at form submission
        \Dsc\System::instance()->get( 'session' )->set( 'user.forgot_password.id', (string) $user->id );
        
        $this->app->set('meta.title', 'Reset Password');
        
        $view = \Dsc\System::instance()->get( 'theme' );
        echo $view->render( 'Users/Site/Views::forgot/password_reset_form.php' );
    }
    
    /**
     * Step 5 == Finally, submit the password reset 
     */
    public function passwordResetSubmit()
    {
        $f3 = \Base::instance();
        
        $data = array(
            'new_password' => $this->input->get( 'new_password', null, 'string' ),
            'confirm_new_password' => $this->input->get( 'confirm_new_password', null, 'string' )
        );
        
        $id = \Dsc\System::instance()->get( 'session' )->get( 'user.forgot_password.id' );
        $user = (new \Users\Models\Users)->setState('filter.id', $id)->getItem();
        if (empty($id) || empty($user->id))
        {
            // session value has expired
            \Dsc\System::addMessage( 'Unable to process password reset request.  Please try again.', 'error' );
            $f3->reroute('/user/forgot-password');
            return;
        }
                
        $user->bind($data);
        
        try
        {
            $user->save()->sendEmailPasswordResetNotification();
        }
        catch(\Exception $e)
        {
            \Dsc\System::addMessage( 'Password reset failed.', 'error' );
            \Dsc\System::addMessage( $e->getMessage(), 'error' );
        
            \Dsc\System::instance()->get( 'session' )->set( 'user.forgot_password.id', null );
            $f3->reroute('/user/forgot-password');        
            return;
        }
        
        // ok, password reset passed
        \Dsc\System::addMessage( 'Password has been reset.  You may now login with your new password.' );
        $f3->reroute('/login');        
    }
}
