<?php 
namespace Users\Admin\Controllers;

class User extends \Admin\Controllers\BaseAuth 
{
	use \Dsc\Traits\Controllers\CrudItemCollection;
	
	protected $list_route = '/admin/users';
	protected $create_item_route = '/admin/user/create';
	protected $get_item_route = '/admin/user/read/{id}';
	protected $edit_item_route = '/admin/user/edit/{id}';
	
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

		$model = $this->getModel('groups');
        $groups = $model->getList();
        \Base::instance()->set('groups', $groups );	

		$view = \Dsc\System::instance()->get('theme');
		$view->event = $view->trigger( 'onDisplayAdminUserEdit', array( 'item' => $this->getItem(), 'tabs' => array(), 'content' => array() ) );

		$this->app->set('meta.title', 'Create User');
		
		$user = $this->getIdentity();
		$canModityRole = false;
		if( $user->role == 'root' ){
			$canModityRole = true;
			$roles = (new \Users\Models\Roles)->getList();
					foreach( $roles as $key => $role ){
				$roles[$key] = array( 'value' => $role->slug, 'text' => $role->title );
			}
			$this->app->set( 'roles', $roles );
		}
		
		$this->app->set( 'canModifyRole', $canModityRole );
		
		echo $view->render('Users/Admin/Views::users/create.php');
	}
	
	protected function displayEdit()
	{
		$f3 = \Base::instance();
		
		$model = $this->getModel('groups');
        $groups = $model->getList();
        \Base::instance()->set('groups', $groups );		

		$view = \Dsc\System::instance()->get('theme');
		$view->event = $view->trigger( 'onDisplayAdminUserEdit', array( 'item' => $this->getItem(), 'tabs' => array(), 'content' => array() ) );
		
		$this->app->set('meta.title', 'Edit User');
		
		$user = $this->getIdentity();
		$canModityRole = false;
		if( $user->role == 'root' ){
			$canModityRole = true;
			$roles = (new \Users\Models\Roles)->getList();
			foreach( $roles as $key => $role ){
				$roles[$key] = array( 'value' => $role->slug, 'text' => $role->title );
			}
			$this->app->set( 'roles', $roles );
		}
		$this->app->set( 'canModifyRole', $canModityRole );
		
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