<?php 
$f3 = \Base::instance();
$global_app_name = $f3->get('APP_NAME');

switch ($global_app_name) 
{
    case "admin":
        // register event listener
        \Dsc\System::instance()->getDispatcher()->addListener(\Users\Listener::instance());
        
 
        // append this app's UI folder to the path
        // new way
        \Dsc\System::instance()->get('theme')->registerViewPath( __dir__ . '/src/Users/Admin/Views/', 'Users/Admin/Views' );
        // old way
        $ui = $f3->get('UI');
        $ui .= ";" . $f3->get('PATH_ROOT') . "vendor/dioscouri/f3-users/src/Users/Admin/Views/";
        $f3->set('UI', $ui);

        // register all the routes
        \Dsc\System::instance()->get('router')->mount( new \Users\Admin\Routes );
        
        break;
    case "site":    
        // new way
        \Dsc\System::instance()->get('theme')->registerViewPath( __dir__ . '/src/Users/Site/Views/', 'Users/Site/Views' );
        // old way
        $ui = $f3->get('UI');
        $ui .= ";" . $f3->get('PATH_ROOT') . "vendor/dioscouri/f3-users/src/Users/Site/Views/";
        $f3->set('UI', $ui);

        // register all the routes
        \Dsc\System::instance()->get('router')->mount( new \Users\Site\Routes );
        
        // TODO set some app-specific settings, if desired
        break;
}
?>