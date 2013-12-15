<?php 
$f3 = \Base::instance();
$global_app_name = $f3->get('APP_NAME');

switch ($global_app_name) 
{
    case "admin":
        // register event listener
        \Dsc\System::instance()->getDispatcher()->addListener(\Users\Listener::instance());
        
        // register all the routes
        $f3->route('GET|POST /admin/users', '\Users\Admin\Controllers\Users->display');
        $f3->route('GET|POST /admin/users/@page', '\Users\Admin\Controllers\Users->display');
        $f3->route('GET /admin/user/edit/@id', '\Users\Admin\Controllers\User->edit');
        
        // append this app's UI folder to the path, e.g. UI=../apps/blog/admin/views/
        
        // TODO set some app-specific settings, if desired
        $ui = $f3->get('UI');
        $ui .= ";" . $f3->get('PATH_ROOT') . "vendor/dioscouri/f3-users/src/Users/Admin/Views/";
        $f3->set('UI', $ui);
                        
        break;
    case "site":
        // TODO register all the routes
        
        // append this app's UI folder to the path, e.g. UI=../apps/blog/site/views/
                
        // TODO set some app-specific settings, if desired
        break;
}
?>