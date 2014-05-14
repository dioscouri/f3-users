<?php
namespace Users\Models;

class Settings extends \Dsc\Mongo\Collections\Settings
{
    public $general = array(
    	'registration' => array(
            'enabled' => 1,
    	    'username' => 1,
            'action' => 'email_validation'
        )
    );
    public $social = array();
    
    protected $__type = 'users.settings';
}