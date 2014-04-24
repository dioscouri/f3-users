<?php 
namespace Users\Admin\Controllers;

class Users extends \Admin\Controllers\BaseAuth 
{
	use \Dsc\Traits\Controllers\AdminList;
	
	protected $list_route = '/admin/users';
	
	protected function getModel($name='Users')
	{
	    switch (strtolower($name)) 
	    {
	        case "group":
	    	case "groups":
	    	    $model = new \Users\Models\Groups;
	    	    break;
	    	default:
	    	    $model = new \Users\Models\Users;
	    	    break;	    	    
	    }
		
		return $model;
	}
        
    public function index()
    {
        $this->checkAccess( __CLASS__, __FUNCTION__ );
        
        $model = $this->getModel();
        $state = $model->populateState()->getState();
        \Base::instance()->set('state', $state );
    
        $paginated = $model->paginate();
        \Base::instance()->set('paginated', $paginated );
        
        $model = $this->getModel('groups');
        $groups = $model->getList();
        \Base::instance()->set('groups', $groups );
        
        $view = \Dsc\System::instance()->get('theme');
        echo $view->render('Users/Admin/Views::users/list.php');
    }
}