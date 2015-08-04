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
    	if (class_exists('\Mailer\Factory')) 
    	{
    		
    		$model = (new \Mailer\Models\Events);
    		
    		\Mailer\Models\Events::register('users.validate_email',
    		    [
    		        'title' => 'New User - Email Validation',
    		        'copy' => 'Sent when validating a newly-registered email address',
    		        'app' => 'Users',
    		    ],
    		    [
    		        'event_subject' => 'Please verify your email address',
    		        'event_html' => file_get_contents(__DIR__.'/Emails/html/validate_email.php'),
    		        'event_text' => file_get_contents(__DIR__.'/Emails/text/validate_email.php')
    		    ]
    		);
    		
    		\Mailer\Models\Events::register('users.password_reset_request',
    		    [
    		        'title' => 'Password Reset Request',
    		        'copy' => 'Sent when a user requests a password reset.',
    		        'app' => 'Users',
    		    ],
    		    [
    		        'event_subject' => 'Password reset request',
    		        'event_html' => file_get_contents(__DIR__.'/Emails/html/password_reset_request.php'),
    		        'event_text' => file_get_contents(__DIR__.'/Emails/text/password_reset_request.php')
    		    ]
    		);
    		
    		\Mailer\Models\Events::register('users.password_reset_notification',
                [
                    'title' => 'Password Reset Notification',
                    'copy' => 'Sent when a user resets their password',
                    'app' => 'Users',
                ],
                [
                    'event_subject' => 'Password reset notification',
                    'event_html' => file_get_contents(__DIR__.'/Emails/html/password_reset_notification.php'),
                    'event_text' => file_get_contents(__DIR__.'/Emails/text/password_reset_notification.php')                    
                ]
    		);

    		\Mailer\Models\Events::register('users.verify_change_email',
    		    [
    		        'title' => 'Email Change Verification',
    		        'copy' => 'Sent when a user changes their email address',
    		        'app' => 'Users',
    		    ],
    		    [
    		        'event_subject' => 'Please verify your email address',
    		        'event_html' => file_get_contents(__DIR__.'/Emails/html/verify_change_email.php'),
    		        'event_text' => file_get_contents(__DIR__.'/Emails/text/verify_change_email.php')
    		    ]
    		);    		
    		
    		\Dsc\System::instance()->addMessage('Users added its emails.');
    	}
    }
    
    public function registerEmails($event)
    {
    	\Dsc\System::instance()->addMessage('Users added its admin menu items.');
    }
    
    public function  mailerPreviewUsersVerify_change_email($event) {
    
    	$options = [];
    
    	$options['user'] = ['first_name'=> 'John', 'last_name' => 'Doe', 'email' => 'fakerandomemail@fakedomain.com'];
    	$options['link'] = \Dsc\Url::base() . 'user/change-email/confirm?new_email&previewview';
    	$options['token'] = new \MongoId();
    
    	
    	$event->setArgument('variables', $options);
    }
    
    public function  mailerPreviewUsersPassword_reset_request($event) {
    
    	$options = [];
    
    	$options['user'] = ['first_name'=> 'John', 'last_name' => 'Doe', 'email' => 'fakerandomemail@fakedomain.com', 'forgot_password' => ['token' => new \MongoId()]];
    	$options['link'] = \Dsc\Url::base() . 'user/change-email/confirm?new_email&previewview';
    	$options['token'] = new \MongoId();
    
    	 
    	$event->setArgument('variables', $options);
    }
    
    public function  mailerPreviewUsersValidate_email($event) {
    
    	$options = [];
    
    	$options['user'] = ['id' => new \MongoId(),'first_name'=> 'John', 'last_name' => 'Doe', 'email' => 'fakerandomemail@fakedomain.com', 'forgot_password' => ['token' => new \MongoId()]];
    	$options['link'] = \Dsc\Url::base() . 'user/change-email/confirm?new_email&previewview';
    	$options['token'] = new \MongoId();
    
    
    	$event->setArgument('variables', $options);
    }
    
    public function  mailerPreviewUsersPassword_reset_notification($event) {
    
    	$options = [];
    
    	$options['user'] = ['id' => new \MongoId(),'first_name'=> 'John', 'last_name' => 'Doe', 'email' => 'fakerandomemail@fakedomain.com', 'forgot_password' => ['token' => new \MongoId()]];
    	$options['link'] = \Dsc\Url::base() . 'user/change-email/confirm?new_email&previewview';
    	$options['token'] = new \MongoId();
    
    
    	$event->setArgument('variables', $options);
    }
    
    
    
    
    
    
   
}