<?php 
namespace Users\Admin\Controllers;

class Users extends \Admin\Controllers\BaseAuth 
{
    public function display()
    {
        \Base::instance()->set('pagetitle', 'Users');
        \Base::instance()->set('subtitle', '');
    
        $model = new \Users\Admin\Collections\Users;
        $state = $model->populateState()->getState();
        \Base::instance()->set('state', $state );
    
        $pagination = $model->paginate();
        \Base::instance()->set('list', array(
        	'subset' => $pagination->items,
            'count' => $pagination->getItemCount()
        ) );
    
        //$pagination = new \Dsc\Pagination($list['total'], $list['limit']);
        \Base::instance()->set('pagination', $pagination );
        
        $model = new \Users\Admin\Models\Groups;
        $groups = $model->getList();
        \Base::instance()->set('groups', $groups ); 
        
        $view = \Dsc\System::instance()->get('theme');
        echo $view->render('Users/Admin/Views::users/list.php');
    }
}