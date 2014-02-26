<?php 
namespace Users\Admin\Controllers;

class Users extends \Admin\Controllers\BaseAuth 
{
    public function display()
    {
        parent::isAllowed( parent::getIdentity(), __NAMESPACE__, __FUNCTION__ );
        
        $model = new \Users\Admin\Models\Users;
        $state = $model->populateState()->getState();
        \Base::instance()->set('state', $state );
    
        $paginated = $model->paginate();
        \Base::instance()->set('paginated', $paginated );
        
        $model = new \Users\Admin\Models\Groups;
        $groups = $model->getList();
        \Base::instance()->set('groups', $groups );
        
        $view = \Dsc\System::instance()->get('theme');
        echo $view->render('Users/Admin/Views::users/list.php');
    }
}