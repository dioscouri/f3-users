<?php 
namespace Users;

class Listener extends \Prefab 
{
    public function onSystemRebuildMenu( $event )
    {
        if ($mapper = $event->getArgument('mapper')) 
        {
        	$mapper->reset();
        	$mapper->priority = 30;
            $mapper->id = 'fa-user';
        	$mapper->title = 'Users';
        	$mapper->route = '';
        	$mapper->icon = 'fa fa-user';
        	$mapper->children = array(
        			json_decode(json_encode(array( 'title'=>'List', 'route'=>'/admin/users', 'icon'=>'fa fa-user' )))
                    ,json_decode(json_encode(array( 'title'=>'Groups', 'route'=>'/admin/users/groups', 'icon'=>'fa fa-group' )))
                    ,json_decode(json_encode(array( 'title'=>'Roles', 'route'=>'/admin/users/roles', 'icon'=>'fa fa-unlock' )))
        	        ,json_decode(json_encode(array( 'title'=>'Settings', 'route'=>'/admin/users/settings', 'icon'=>'fa fa-cogs' )))
           
            );
        	$mapper->save();
        	
        	\Dsc\System::instance()->addMessage('Users added its admin menu items.');
        }
        
    }
}