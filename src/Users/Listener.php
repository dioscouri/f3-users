<?php
namespace Users;

class Listener extends \Prefab
{

    public function onSystemRebuildMenu($event)
    {
        if ($model = $event->getArgument('model'))
        {
            $root = $event->getArgument('root');
            $users = clone $model;
            
            $users->insert(array(
                'type' => 'admin.nav',
                'priority' => 50,
                'title' => 'Users',
                'icon' => 'fa fa-user',
                'is_root' => false,
                'tree' => $root,
                'base' => '/admin/users'
            ));
            
            $children = array(
                array(
                    'title' => 'List',
                    'route' => './admin/users',
                    'icon' => 'fa fa-user'
                ),
                array(
                    'title' => 'Groups',
                    'route' => './admin/users/groups',
                    'icon' => 'fa fa-group'
                ),
                array(
                    'title' => 'Roles',
                    'route' => './admin/users/roles',
                    'icon' => 'fa fa-unlock'
                ),
                array(
                    'title' => 'Settings',
                    'route' => './admin/users/settings',
                    'icon' => 'fa fa-cogs'
                )
            );
            $users->addChildren($children, $root);
            
            \Dsc\System::instance()->addMessage('Users added its admin menu items.');
        }
    }
    
    public function onSystemRegisterEmails($event)
    {
    	if(class_exists('\Mailer\Factory')) {
    		
    		$model = (new \Mailer\Models\Events);
    		
    		
    		\Mailer\Models\Events::register('usersEmailPasswordResetNotification',
    				 [
    				 'title' => 'Users Email Password Reset Notification',
    				 'copy' => 'Sent when a user resets their password',
    				 'app' => 'Users',
    				 ],
    				[
    				  'event_title' => 'Password reset notification',
    				  'event_html' => file_get_contents(__DIR__.'/Emails/html/password_reset_notification.php'),
    				  'event_text' => file_get_contents(__DIR__.'/Emails/text/password_reset_notification.php')
    				]
    		);
    		
    		/*\Mailer\Models\Events::register('usersNewUserCreated', ['html_content' => file_get_contents(__DIR__.'Emails/html/'), 'text_content' => file_get_contents(__DIR__.'Emails/html/')  ]);
    		\Mailer\Models\Events::register('usersEmailChangeEmailConfirmation');
    		\Mailer\Models\Events::register('usersEmailResetPassword');
    		\Mailer\Models\Events::register('usersEmailValidatingEmailAddress');*/
    		
    		
    		
    		
    		\Dsc\System::instance()->addMessage('Users added its emails.');
    		
    	}
    	
    	
    	
    }
    
    public function registerEmails($event)
    {
    	\Dsc\System::instance()->addMessage('Users added its admin menu items.');
    }
}