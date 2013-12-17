<?php 
namespace Users;

class Listener extends \Prefab 
{
    public function onSystemRebuildMenu( $event )
    {
        if ($mapper = $event->getArgument('mapper')) 
        {
        	$mapper->reset();
            $mapper->id = 'fa-user';
        	$mapper->title = 'Users';
        	$mapper->route = '';
        	$mapper->icon = 'fa fa-user';
        	$mapper->children = array(
        			json_decode(json_encode(array( 'title'=>'List', 'route'=>'/admin/users', 'icon'=>'fa fa-list' )))
        			,json_decode(json_encode(array( 'title'=>'Add New', 'route'=>'/admin/user', 'icon'=>'fa fa-plus' )))
        			,json_decode(json_encode(array( 'title'=>'Detail', 'route'=>'/admin/user/view', 'hidden'=>true )))
        	);
        	$mapper->save();
        	
        	\Dsc\System::instance()->addMessage('Users added its admin menu items.');
        }
        
    }
}