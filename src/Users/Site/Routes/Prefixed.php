<?php
namespace Users\Site\Routes;

class Prefixed extends \Dsc\Routes\Group
{
    public function initialize()
    {
        $f3 = \Base::instance();
        
        $this->setDefaults(array(
            'namespace' => '\Users\Site\Controllers',
            'url_prefix' => '/user'
        ));
        
        $this->add('', 'GET', array(
            'controller' => 'User',
            'action' => 'readSelf'
        ));
        
        $this->add('/@id', 'GET', array(
            'controller' => 'User',
            'action' => 'read'
        ));
        
        $this->add('/forgot-password', 'GET', array(
            'controller' => 'Forgot',
            'action' => 'password'
        ));
        
        $this->add('/forgot-password', 'POST', array(
            'controller' => 'Forgot',
            'action' => 'passwordFindEmail'
        ));
        
        $this->add('/forgot-password/email', 'GET', array(
            'controller' => 'Forgot',
            'action' => 'passwordEmailSent'
        ));
        
        $this->add('/reset-password/@token', 'GET', array(
            'controller' => 'Forgot',
            'action' => 'passwordReset'
        ));
        
        $this->add('/reset-password', 'POST', array(
            'controller' => 'Forgot',
            'action' => 'passwordResetSubmit'
        ));
        
        $this->add('/change-password', 'GET', array(
            'controller' => 'Change',
            'action' => 'password'
        ));
        
        $this->add('/change-password', 'POST', array(
            'controller' => 'Change',
            'action' => 'passwordSubmit'
        ));
        
        $this->add('/change-email', 'GET', array(
            'controller' => 'Change',
            'action' => 'email'
        ));
        
        $this->add('/change-basic', 'GET', array(
        		'controller' => 'Change',
        		'action' => 'basicInfo'
        ));

        $this->add('/change-basic', 'POST', array(
        		'controller' => 'Change',
        		'action' => 'basicInfoSubmit'
        ));
        
        $this->add('/change-email', 'POST', array(
            'controller' => 'Change',
            'action' => 'emailSubmit'
        ));
        
        $this->add('/change-email/verify', 'GET', array(
            'controller' => 'Change',
            'action' => 'emailVerify'
        ));
        
        $this->add('/change-email/confirm', 'GET|POST', array(
            'controller' => 'Change',
            'action' => 'emailConfirm'
        ));
        
        $this->add('/social-profiles', 'GET', array(
            'controller' => 'User',
            'action' => 'socialProfiles'
        ));
        
        $this->add('/social/unlink/@provider', 'GET', array(
            'controller' => 'User',
            'action' => 'unlinkSocialProfile'
        ));
        
        $this->add('/social/link/@provider', 'GET', array(
            'controller' => 'User',
            'action' => 'linkSocialProfile'
        ));
    }
}