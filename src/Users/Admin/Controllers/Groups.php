<?php 
namespace Users\Admin\Controllers;

class Groups extends \Admin\Controllers\BaseAuth 
{   
     protected function getModel()
    {
        $model = new \Users\Admin\Models\Groups;
        return $model;
    }

    public function index()
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
    
        $view = \Dsc\System::instance()->get('theme');
        echo $view->render('Users/Admin/Views::groups/list.php');
    }


    public function getCheckboxes()
    {
        $model = $this->getModel();
        $groups = $model->getList();
        \Base::instance()->set('groups', $groups );
    
        $selected = array();
        $data = \Base::instance()->get('REQUEST');
        
        $input = $data['groups_ids'];
        foreach ($input as $id) 
        {
            $id = $this->inputfilter->clean( $id, 'alnum' );
            $selected[] = array('id' => $id);
        }

        $flash = \Dsc\Flash::instance();
        $flash->store( array( 'metadata'=>array('groups'=>$selected) ) );
        \Base::instance()->set('flash', $flash );
        
        $view = \Dsc\System::instance()->get('theme');
        $html = $view->renderLayout('Users/Admin/Views::groups/checkboxes.php');
    
        return $this->outputJson( $this->getJsonResponse( array(
                'result' => $html
        ) ) );
    
    }
}