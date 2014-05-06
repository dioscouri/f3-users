<?php 
namespace Users\Admin\Controllers;

class Role extends \Admin\Controllers\BaseAuth 
{
	use \Dsc\Traits\Controllers\CrudItemCollection;
	
	protected $list_route = '/admin/users/roles';
	protected $create_item_route = '/admin/users/role/create';
	protected $get_item_route = '/admin/users/role/read/{id}';
	protected $edit_item_route = '/admin/users/role/edit/{id}';
	
	protected function getModel($name='Roles')
	{
	    switch (strtolower($name)) 
	    {
	    	default:
	    	    $model = new \Users\Models\Roles;
	    	    break;	    	    
	    }
		
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
	    $parents = $this->getModel()->getItems();
	    \Base::instance()->set('parents', $parents );
	    	    
		$view = \Dsc\System::instance()->get('theme');
		$view->event = $view->trigger( 'onDisplayAdminRoleEdit', array( 'item' => $this->getItem(), 'tabs' => array(), 'content' => array() ) );
		
		echo $view->render('Users/Admin/Views::roles/create.php');
	}
	
	protected function displayEdit()
	{
	    $parents = $this->getModel()->getItems();
	    \Base::instance()->set('parents', $parents );

		$view = \Dsc\System::instance()->get('theme');
		$view->event = $view->trigger( 'onDisplayAdminRoleEdit', array( 'item' => $this->getItem(), 'tabs' => array(), 'content' => array() ) );
				
		echo $view->render('Users/Admin/Views::roles/edit.php');
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