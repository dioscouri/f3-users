<?php
namespace Users\Site\Controllers;

class Change extends \Users\Site\Controllers\Auth
{
    public function password()
    {
        $view = \Dsc\System::instance()->get( 'theme' );
        echo $view->render( 'Users/Site/Views::change/password.php' );
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
}
