<?php 
$f3 = \Base::instance();
$global_app_name = $f3->get('APP_NAME');

switch ($global_app_name) 
{
    case "admin":
        // register event listener
        \Dsc\System::instance()->getDispatcher()->addListener(\Users\Listener::instance());
        
        // register all the routes
        // settings routes
        $f3->route('GET /admin/users/settings', '\Users\Admin\Controllers\Settings->display');
        $f3->route('POST /admin/users/settings', '\Users\Admin\Controllers\Settings->save');
        // users list
        $f3->route('GET|POST /admin/users', '\Users\Admin\Controllers\Users->display');
        $f3->route('GET|POST /admin/users/page/@page', '\Users\Admin\Controllers\Users->display');
        $f3->route('GET|POST /admin/users/delete', '\Users\Admin\Controllers\Users->delete');        
        // user crud
        $f3->route('GET /admin/user/create', '\Users\Admin\Controllers\User->create');
        $f3->route('POST /admin/user/add', '\Users\Admin\Controllers\User->add');
        $f3->route('GET /admin/user/read/@id', '\Users\Admin\Controllers\User->read');
        $f3->route('GET /admin/user/edit/@id', '\Users\Admin\Controllers\User->edit');
        $f3->route('POST /admin/user/update/@id', '\Users\Admin\Controllers\User->update');
        $f3->route('GET|DELETE /admin/user/delete/@id', '\Users\Admin\Controllers\User->delete');
        // groups list
        $f3->route('GET|POST /admin/users/groups', '\Users\Admin\Controllers\Groups->display');
        $f3->route('GET|POST /admin/users/groups/page/@page', '\Users\Admin\Controllers\Groups->display');
        $f3->route('GET|POST /admin/users/groups/delete', '\Users\Admin\Controllers\Groups->delete');
        // groups crud
        $f3->route('GET /admin/users/group/create', '\Users\Admin\Controllers\Group->create');
        $f3->route('POST /admin/users/group/add', '\Users\Admin\Controllers\Group->add');
        $f3->route('GET /admin/users/group/read/@id', '\Users\Admin\Controllers\Group->read');
        $f3->route('GET /admin/users/group/edit/@id', '\Users\Admin\Controllers\Group->edit');
        $f3->route('POST /admin/users/group/update/@id', '\Users\Admin\Controllers\Group->update');
        $f3->route('GET|DELETE /admin/users/group/delete/@id', '\Users\Admin\Controllers\Group->delete');
 
        // append this app's UI folder to the path
        // new way
        \Dsc\System::instance()->get('theme')->registerViewPath( __dir__ . '/src/Users/Admin/Views/', 'Users/Admin/Views' );
        // old way
        $ui = $f3->get('UI');
        $ui .= ";" . $f3->get('PATH_ROOT') . "vendor/dioscouri/f3-users/src/Users/Admin/Views/";
        $f3->set('UI', $ui);
                        
        break;
    case "site":    
        // new way
        \Dsc\System::instance()->get('theme')->registerViewPath( __dir__ . '/src/Users/Site/Views/', 'Users/Site/Views' );
        // old way
        $ui = $f3->get('UI');
        $ui .= ";" . $f3->get('PATH_ROOT') . "vendor/dioscouri/f3-users/src/Users/Site/Views/";
        $f3->set('UI', $ui);

        // TODO register all the routes
        $f3->route('GET /signup', '\Users\Site\Controllers\Auth->showSignup');
        $f3->route('GET /login', '\Users\Site\Controllers\Auth->showLogin'); 
        $f3->route('POST /signup', '\Users\Site\Controllers\Auth->doSignup');
        $f3->route('POST /login', '\Users\Site\Controllers\Auth->doLogin');
        $f3->route('GET|POST /logout', '\Users\Site\Controllers\User->logout'); 

        // TODO set some app-specific settings, if desired
        break;
}
?>