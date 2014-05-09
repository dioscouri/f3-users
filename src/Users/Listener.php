<?php 
namespace Users;

class Listener extends \Prefab 
{
    public function onSystemRebuildMenu( $event )
    {
		if ($model = $event->getArgument('model'))
		{
			$root = $event->getArgument( 'root' );
			$users = clone $model;
        		 
			$users->insert(
					array(
						'type'	=> 'admin.nav',
						'priority' => 50,
						'title'	=> 'Users',
						'icon'	=> 'fa fa-user',
        				'is_root' => false,
						'tree'	=> $root,
						'base' => '/admin/users',
					)
				);
        	
			$children = array(
        			array( 'title'=>'List', 'route'=>'/admin/users', 'icon'=>'fa fa-user' ),
                    array( 'title'=>'Groups', 'route'=>'/admin/users/groups', 'icon'=>'fa fa-group' ),
                    array( 'title'=>'Roles', 'route'=>'/admin/users/roles', 'icon'=>'fa fa-unlock' ),
        	        array( 'title'=>'Settings', 'route'=>'/admin/users/settings', 'icon'=>'fa fa-cogs' ),
			);
       		$users->addChildrenItems( $children, $root, $model );
        	
        	\Dsc\System::instance()->addMessage('Users added its admin menu items.');
        }
        
    }
}