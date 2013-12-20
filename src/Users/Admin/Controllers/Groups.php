<?php 
namespace Users\Admin\Controllers;

class Groups extends \Admin\Controllers\BaseAuth 
{
    public function display()
    {
        \Base::instance()->set('pagetitle', 'User Groups');
        \Base::instance()->set('subtitle', '');
    
        $model = new \Users\Admin\Models\Groups;
        $state = $model->populateState()->getState();
        \Base::instance()->set('state', $state );
    
        $list = $model->paginate();
        \Base::instance()->set('list', $list );
    
        $pagination = new \Dsc\Pagination($list['total'], $list['limit']);
        \Base::instance()->set('pagination', $pagination );
    
        $view = new \Dsc\Template;
        echo $view->render('Users/Admin/Views::groups/list.php');
    }
}