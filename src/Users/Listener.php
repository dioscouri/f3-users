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
        			json_decode(json_encode(array( 'title'=>'List', 'route'=>'/admin/users', 'icon'=>'fa fa-list' )))
                    ,json_decode(json_encode(array( 'title'=>'Groups', 'route'=>'/admin/users/groups', 'icon'=>'fa fa-list' )))

        	);
        	$mapper->save();
        	
        	\Dsc\System::instance()->addMessage('Users added its admin menu items.');
        }
        
    }
}