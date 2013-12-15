<?php 
namespace Users\Admin\Controllers;

class Users extends \Admin\Controllers\BaseAuth 
{
    public function display()
    {
        \Base::instance()->set('pagetitle', 'Users');
        \Base::instance()->set('subtitle', '');
    
        $model = new \Users\Admin\Models\Users;
        $state = $model->populateState()->getState();
        \Base::instance()->set('state', $state );
    
        $list = $model->paginate();
        \Base::instance()->set('list', $list );
    
        $pagination = new \Dsc\Pagination($list['total'], $list['limit']);
        \Base::instance()->set('pagination', $pagination );
    
        $view = new \Dsc\Template;
        echo $view->render('Users/Admin/Views::users/list.php');
    }
}