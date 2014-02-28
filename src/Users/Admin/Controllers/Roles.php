<?php 
namespace Users\Admin\Controllers;

class Roles extends \Admin\Controllers\BaseAuth 
{
	protected function getModel($name='Role')
	{
	    switch (strtolower($name)) 
	    {
	    	default:
	    	    $model = new \Users\Models\Roles;
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
        
        $view = \Dsc\System::instance()->get('theme');
        echo $view->render('Users/Admin/Views::roles/list.php');
    }
}