<?php 
namespace Users\Admin\Controllers;

class User extends \Admin\Controllers\BaseAuth 
{
	use \Dsc\Traits\Controllers\CrudItem;
	
	protected $list_route = '/admin/users';
	protected $create_item_route = '/admin/user';
	protected $get_item_route = '/admin/user/{id}';
	protected $edit_item_route = '/admin/user/{id}/edit';
	
	protected function getModel()
	{
		$model = new \Users\Admin\Models\Users;
		return $model;
	}
	
	protected function getItem()
	{
		$f3 = \Base::instance();
		$id = $this->inputfilter->clean( $f3->get('PARAMS.id'), 'alnum' );
		$model = $this->getModel()
		->setState('filter.id', $id);
	
		try {
			$item = $model->getItem();
		} catch ( \Exception $e ) {
			\Dsc\System::instance()->addMessage( "Invalid Item: " . $e->getMessage(), 'error');
			$f3->reroute( $this->list_route );
			return;
		}
	
		return $item;
	}
	
	protected function displayCreate()
	{
		$f3 = \Base::instance();
		$f3->set('pagetitle', 'Create User');
	
		$view = new \Dsc\Template;
		echo $view->render('Users/Admin/Views::users/create.php');
	}
	
	protected function displayEdit()
	{
		$f3 = \Base::instance();
		$f3->set('pagetitle', 'Edit User');
	
		$view = new \Dsc\Template;
		echo $view->render('Users/Admin/Views::users/edit.php');
	}
	
	/**
	 * This controller doesn't allow reading, only editing, so redirect to the edit method
	 */
	protected function doRead(array $data, $key=null)
	{
		$f3 = \Base::instance();
		$id = $this->getItem()->get( $this->getItemKey() );
		$route = str_replace('{id}', $id, $this->edit_item_route );
		$f3->reroute( $route );
	}
	
	protected function displayRead() {}
		
	/*
    public function get()
    {
        $id = $this->inputfilter->clean( \Base::instance()->get('PARAMS.id'), 'alnum' );
        
        $model = new \Users\Admin\Models\Users;
        $model->setState('filter.id', $id);
        $state = $model->getState();
        \Base::instance()->set('state', $state );

        try {
            $item = $model->getItem();
        } catch ( \Exception $e ) {
            \Dsc\System::instance()->addMessage( "Invalid User: " . $e->getMessage(), 'error');
            \Base::instance()->reroute("/admin/users");
            return;            
        }
        
        \Base::instance()->set('item', $item );
        
        if (empty($item->_id) || $item->_id != $id) {
            \Dsc\System::instance()->addMessage('Invalid ID', 'error');
            \Base::instance()->reroute("/admin/users");
            return;
        }
        
        \Base::instance()->set('pagetitle', 'User Detail');
        \Base::instance()->set('subtitle', '');
        
        $view = new \Dsc\Template;
        echo $view->render('Users/Admin/Views::users/detail.php');
    }
    
    public function post()
    {
    
    }
    
    public function edit()
    {
        $id = $this->inputfilter->clean( \Base::instance()->get('PARAMS.id'), 'alnum' );
        
        $model = new \Users\Admin\Models\Users;
        $model->setState('filter.id', $id);
        $state = $model->getState();
        \Base::instance()->set('state', $state );
        
        $item = $model->getItem();
        \Base::instance()->set('item', $item );

        if (empty($item->_id) || $item->_id != $id) {
            \Dsc\System::instance()->addMessage('Invalid ID', 'error');
            \Base::instance()->reroute("/admin/users");
            return;
        }
        
        \Base::instance()->set('pagetitle', 'Edit User');
        \Base::instance()->set('subtitle', '');
        
        $view = new \Dsc\Template;
        echo $view->render('Users/Admin/Views::users/edit.php');
    }
    */
}