<?php 
namespace Users\Admin\Controllers;

class Settings extends \Admin\Controllers\BaseAuth 
{
	use \Dsc\Traits\Controllers\Settings;
	
	protected $layout_link = 'Users/Admin/Views::settings/default.php';
	protected $settings_route = '/admin/users/settings';
    
    protected function getModel()
    {
        $model = new \Users\Models\Settings;
        return $model;
    }
}