<?php 
namespace Users;

class Listener extends \Prefab 
{
    public function onSystemRebuildMenu( $event )
    {
        if ($mapper = $event->getArgument('mapper')) 
        {
        	$mapper->reset();
        	$mapper->title = 'Users';
        	$mapper->route = '';
        	$mapper->icon = 'fa fa-user';
        	$mapper->children = array(
        			json_decode(json_encode(array( 'title'=>'List', 'route'=>'/admin/users', 'icon'=>'fa fa-list' )))
        			,json_decode(json_encode(array( 'title'=>'Detail', 'route'=>'/admin/user', 'hidden'=>true )))
        	);
        	$mapper->save();
        	
        	\Dsc\System::instance()->addMessage('Users added its admin menu items.');
        }
        
    }
}