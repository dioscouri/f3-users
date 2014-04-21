<?php
namespace Users\Models;

class Settings extends \Dsc\Mongo\Collections\Settings
{
    public $social = array();
    
    protected $__type = 'users.settings';
}