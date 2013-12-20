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
        $f3->route('GET|POST /admin/users/delete', '\Users\Admin\Controllers\Users->delete');
        $f3->route('GET /admin/user', '\Users\Admin\Controllers\User->create');
        $f3->route('POST /admin/user', '\Users\Admin\Controllers\User->add');
        $f3->route('GET /admin/user/@id', '\Users\Admin\Controllers\User->read');
        $f3->route('GET /admin/user/@id/edit', '\Users\Admin\Controllers\User->edit');
        $f3->route('POST /admin/user/@id', '\Users\Admin\Controllers\User->update');
        $f3->route('DELETE /admin/user/@id', '\Users\Admin\Controllers\User->delete');
        $f3->route('GET /admin/user/@id/delete', '\Users\Admin\Controllers\User->delete');        
        //GROUPS ROUTES
        $f3->route('GET|POST /admin/users/groups', '\Users\Admin\Controllers\Groups->display');
        $f3->route('GET|POST /admin/users/groups/@page', '\Users\Admin\Controllers\Groups->display');
        $f3->route('GET|POST /admin/users/groups/delete', '\Users\Admin\Controllers\Groups->delete');
        $f3->route('GET /admin/users/group', '\Users\Admin\Controllers\Group->create');
        $f3->route('POST /admin/users/group', '\Users\Admin\Controllers\Group->add');
        $f3->route('GET /admin/users/group/@id', '\Users\Admin\Controllers\Group->read');
        $f3->route('GET /admin/users/group/@id/edit', '\Users\Admin\Controllers\Group->edit');
        $f3->route('POST /admin/users/group/@id', '\Users\Admin\Controllers\Group->update');
        $f3->route('DELETE /admin/users/group/@id', '\Users\Admin\Controllers\Group->delete');
        $f3->route('GET /admin/users/group/@id/delete', '\Users\Admin\Controllers\Group->delete'); 
       // $f3->route('GET|POST  /admin/users/groups/checkboxes', '\Users\Admin\Controllers\Groups->getCheckboxes');
 
        // append this app's UI folder to the path, e.g. UI=../apps/blog/admin/views/
        
        // TODO set some app-specific settings, if desired
        $ui = $f3->get('UI');
        $ui .= ";" . $f3->get('PATH_ROOT') . "vendor/dioscouri/f3-users/src/Users/Admin/Views/";
        $f3->set('UI', $ui);
                        
        break;
    case "site":    
        //TODO if this section is removed it will load the UI from your apps/site app or whereever you set your ui, maybe we
        $ui = $f3->get('UI');
        $ui .= ";" . $f3->get('PATH_ROOT') . "vendor/dioscouri/f3-users/src/Users/Site/Views/";
        $f3->set('UI', $ui);

        $f3->route('GET /signup', '\Users\Site\Controllers\Auth->showSignup');
        $f3->route('GET /login', '\Users\Site\Controllers\Auth->showLogin'); 
        $f3->route('POST /signup', '\Users\Site\Controllers\Auth->doSignup');
        $f3->route('POST /login', '\Users\Site\Controllers\Auth->doLogin');
        $f3->route('GET|POST /logout', '\Users\Site\Controllers\User->logout'); 


        // TODO register all the routes
        
        // append this app's UI folder to the path, e.g. UI=../apps/blog/site/views/
                
        // TODO set some app-specific settings, if desired
        break;
}
?>