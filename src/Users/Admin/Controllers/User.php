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

		$model = new \Users\Admin\Models\Groups;
        $groups = $model->getList();
        \Base::instance()->set('groups', $groups );	

		$view = new \Dsc\Template;
		$view->event = $view->trigger( 'onDisplayAdminUserEdit', array( 'item' => $this->getItem(), 'tabs' => array(), 'content' => array() ) );
		
		echo $view->render('Users/Admin/Views::users/create.php');
	}
	
	protected function displayEdit()
	{
		$f3 = \Base::instance();
		$f3->set('pagetitle', 'Edit User');
		
		$model = new \Users\Admin\Models\Groups;
        $groups = $model->getList();
        \Base::instance()->set('groups', $groups );		

		$view = new \Dsc\Template;
		$view->event = $view->trigger( 'onDisplayAdminUserEdit', array( 'item' => $this->getItem(), 'tabs' => array(), 'content' => array() ) );
				
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
}