<?php
namespace Users\Models;

class Users extends \Dsc\Mongo\Collections\Taggable
{
    public $username;
    public $password;
    public $first_name;
    public $last_name;
    public $email;
    public $role = null;
    public $active = true;
    public $banned = false;
    public $suspended = false;
    public $social = array();
    public $groups = array();
    public $photo;
    public $last_visit = array();
    public $admin_tags = array();
    
    protected $__collection_name = 'users';
    protected $__type = 'users';
    
    protected $__config = array(
        'default_sort' => array(
            'last_visit.time' => -1
        )
    );

    const E_EMAIL_EXISTS = 1;

    protected function fetchConditions()
    {
        parent::fetchConditions();
        
        $filter_keyword = $this->getState('filter.keyword');
        if ($filter_keyword && is_string($filter_keyword))
        {
            $key = new \MongoRegex('/' . $filter_keyword . '/i');
            
            $where = array();
            $where[] = array(
                'username' => $key
            );
            $where[] = array(
                'email' => $key
            );
            $where[] = array(
                'first_name' => $key
            );
            $where[] = array(
                'last_name' => $key
            );
            
            $this->setCondition('$or', $where);
        }
        
        $filter_username = $this->getState('filter.username', null, 'alnum');
        if (strlen($filter_username))
        {
            $this->setCondition('username', $filter_username);
        }
        
        $filter_username_contains = $this->getState('filter.username-contains', null, 'alnum');
        if (strlen($filter_username_contains))
        {
            $key = new \MongoRegex('/' . $filter_username_contains . '/i');
            $this->setCondition('username', $key);
        }
        
        $filter_email = $this->getState('filter.email');
        if (strlen($filter_email))
        {
            $this->setCondition('email', $filter_email);
        }
        
        $filter_email_contains = $this->getState('filter.email-contains');
        if (strlen($filter_email_contains))
        {
            $key = new \MongoRegex('/' . $filter_email_contains . '/i');
            $this->setCondition('email', $key);
        }
        
        $filter_password = $this->getState('filter.password');
        if (strlen($filter_password))
        {
            $this->setCondition('password', $filter_password);
        }
        
        $filter_group = $this->getState('filter.group');        
        if (strlen($filter_group))
        {
            $this->setCondition('groups.id', new \MongoId((string) $filter_group));
        }
        
        $filter_forgot_password_token = $this->getState('filter.forgot_password.token');
        if (strlen($filter_forgot_password_token))
        {
            $this->setCondition('forgot_password.token', (string) $filter_forgot_password_token);
        }

        $filter_new_email = $this->getState('filter.new_email');
        if (strlen($filter_new_email))
        {
            $this->setCondition('change_email.email', $filter_new_email);
        }
        
        $filter_new_email_token = $this->getState('filter.new_email_token');
        if (strlen($filter_new_email_token))
        {
            $this->setCondition('change_email.token', (string) $filter_new_email_token);
        }
        
        $filter_social_profile = $this->getState('filter.social-profile');
        if (strlen($filter_social_profile))
        {
        	$this->setCondition('social.'.$filter_social_profile, array( '$exists' => true ) );
        }
        
        $filter_admin_tags = (array) $this->getState('filter.admin_tags');
        if (!empty($filter_admin_tags))
        {
            $filter_admin_tags = array_filter( array_values( $filter_admin_tags ), function( $var ) {return !empty( trim($var) ); } );
            
            if (!empty($filter_admin_tags)) {
                if( count( $filter_admin_tags ) == 1 && $filter_admin_tags[0] == '--' ) {
                    
                    if (!$and = $this->getCondition('$and'))
                    {
                        $and = array();
                    }
                    
                    $and[] = array(
                        '$or' => array(
                            array(
                                'admin_tags' => null
                            ),
                            array(
                                'admin_tags' => array(
                                    '$size' => 0
                                )
                            )
                        )
                    );
                    
                    $this->setCondition('$and', $and);
                    
                } else {
                    $this->setCondition('admin_tags', array( '$in' => $filter_admin_tags ) );
                }
                 
            }            
        }
        
        $filter_last_visit_after = $this->getState('filter.last_visit_after');
        if (strlen($filter_last_visit_after))
        {
            $filter_last_visit_after = strtotime($filter_last_visit_after);
        
            // add $and conditions to the query stack
            if (!$and = $this->getCondition('$and'))
            {
                $and = array();
            }
        
            $and[] = array(
                '$or' => array(
                    array(
                        'last_visit.time' => null
                    ),
                    array(
                        'last_visit.time' => array(
                            '$gte' => $filter_last_visit_after
                        )
                    )
                )
            );
        
            $this->setCondition('$and', $and);
        }
        
        $filter_last_visit_before = $this->getState('filter.last_visit_before');
        if (strlen($filter_last_visit_before))
        {
            $filter_last_visit_before = strtotime($filter_last_visit_before);
        
            // add $and conditions to the query stack
            if (!$and = $this->getCondition('$and'))
            {
                $and = array();
            }
        
            $and[] = array(
                '$or' => array(
                    array(
                        'last_visit.time' => 0
                    ),                    
                    array(
                        'last_visit.time' => array(
                            '$lte' => $filter_last_visit_before
                        )
                    )
                )
            );
        
            $this->setCondition('$and', $and);
        }        
                
        return $this;
    }

    protected function beforeValidate()
    {
        if (! empty($this->new_password))
        {
            if (empty($this->confirm_new_password))
            {
                $this->setError('Must confirm new password');
            }
            
            if ($this->new_password != $this->confirm_new_password)
            {
                $this->setError('New password and confirmation value do not match');
            }
            
            $this->password = password_hash($this->new_password, PASSWORD_DEFAULT);
        }
        
        unset($this->new_password);
        unset($this->confirm_new_password);
        
        $user = \Dsc\System::instance()->get('auth')->getIdentity();
        
        if( $user->role != 'root' ){ // do not allow to change user role unless the current user is in root group
        	$old_role = 'unidentified';
        	if( !empty( $this->id )){
        		$old_user = (new \Users\Models\Users )->setState( 'filter.id', $this->id )->getItem();
        		$old_role = $old_user->role;
        	}
        	$this->role = $old_role;
        }
        
        
        if (empty($this->password))
        {
            $this->__auto_password = $this->generateRandomString(10); // save this for later emailing to the user, if necessary
            $this->password = password_hash($this->__auto_password, PASSWORD_DEFAULT);
        }
        
        return parent::beforeValidate();
    }

    public function validate()
    {
        $this->email = trim( strtolower( $this->email ) );
        
        // if you want, use $this->validateWith( $validator ) here
        if (empty($this->email))
        {
            $this->setError('Email is required');
        }
        
        if (empty($this->password))
        {
            $this->setError('Password is required');
        }
        
        // is the email unique?
        // this would be a great case for $this->validateWith( $validator ); -- using a Uniqueness Validator
        if (!empty($this->email) && $existing = $this->emailExists( $this->email ))
        {
            if ((empty($this->id) || $this->id != $existing->id))
            {
                $this->setError('This email is already registered');
            }
        }
        
        if (!empty($this->username))
        {
            $this->username = static::usernameFromString( $this->username );
        }        
        
        if (empty($this->username))
        {
            $this->username = $this->generateUsername();
        }
        
        // is the username unique?
        // this would be a great case for $this->validateWith( $validator ); -- using a Uniqueness Validator
        if (!empty($this->username) && $existing = $this->usernameExists( $this->username ))
        {
            if ((empty($this->id) || $this->id != $existing->id))
            {
                $this->setError('This username is taken');
            }
        }
        
        return parent::validate();
    }

    protected function beforeSave()
    {
        $googleProfilePhoto = $this->{'social.google.profile.photoURL'};
        if (strlen($googleProfilePhoto) && strpos($googleProfilePhoto, 'sz=50') !== false) 
        {
            $this->{'social.google.profile.photoURL'} = str_replace('sz=50', '', $googleProfilePhoto);
        }
        
        if (!empty($this->__groups))
        {
            if (is_string($this->__groups)) {
                $this->__groups = \Base::instance()->split( $this->__groups );
            }            
            
            $groups = array();
            foreach ($this->__groups as $key => $id)
            {
                $item = (new \Users\Models\Groups())->setState('filter.id', $id)->getItem();
                $groups[] = array(
                    "id" => $item->id,
                    "title" => $item->title,
                    "slug" => $item->slug
                );
            }
            $this->groups = $groups;
        }
        
        // ensure that groups are unique
        if (!empty($this->groups))
        {
            $groups = array();
            foreach ($this->groups as $key => $id)
            {
                if (is_array($id))
                {
                    if (!empty($id['id'])) 
                    {
                        $group_id = $id['id'];
                        if (!array_key_exists((string) $group_id, $groups)) 
                        {
                            $groups[(string) $group_id] = $id;
                        }
                    }
                }
                elseif (is_string($id))
                {
                    if (!array_key_exists((string) $id, $groups))
                    {
                        $item = (new \Users\Models\Groups())->setState('filter.id', $id)->getItem();
                        if (!empty($item->id)) 
                        {
                            $groups[(string) $item->id] = array(
                                "id" => $item->id,
                                "title" => $item->title,
                                "slug" => $item->slug
                            );
                        }
                    }                    
                }
            }
            
            $this->groups = array_values($groups);
        }
        
        if (!empty($this->admin_tags) && !is_array($this->admin_tags))
        {
            $this->admin_tags = trim($this->admin_tags);
            if (!empty($this->admin_tags)) {
                $this->admin_tags = array_map(function($el){
                    return strtolower($el);
                }, \Base::instance()->split( (string) $this->admin_tags ));
            }
        }
        elseif(empty($this->admin_tags) && !is_array($this->admin_tags))
        {
            $this->admin_tags = array();
        }
        
        return parent::beforeSave();
    }

    /**
     * Generates a random password string
     * 
     * @param number $length            
     * @return string
     */
    public function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
        $randomString = '';
        for ($i = 0; $i < $length; $i ++)
        {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $randomString;
    }
    
    /**
     *
     * @param string $unique
     * @return string
     */
    public function generateUsername( $unique=true )
    {
        $mongo_id = new \MongoId;
        
        $username = !empty($this->username) ? $this->username : $this->email;
        if (empty($username)) {
            return $mongo_id;
        }
    
        $pieces = explode('@', $username);
        $username = $pieces[0];
        if (empty($username)) {
            return $mongo_id;
        }
            
        $username = $this->usernameFromString( $username );
        if (empty($username)) {
            return $mongo_id;
        }

        if ($unique)
        {
            $base = $username;
            while ($this->usernameExists($username))
            {
                if (empty($mongo_id)) {
                    $mongo_id = new \MongoId;
                }
                $username = $base . $mongo_id;
                unset($mongo_id);
            }
        }
    
        return $username;
    }

    /**
     * Gets the user's full name
     * @return unknown
     */
    public function fullName()
    {
        $name = trim($this->first_name . " " . $this->last_name);
        return $name;
    }
    
    /**
     *
     *
     * @param string $slug
     * @return unknown|boolean
     */
    public static function emailExists( $email )
    {
        $clone = (new static)->load(array('email'=>$email));
    
        if (!empty($clone->id)) {
            return $clone;
        }
    
        return false;
    }
    
    /**
     *
     *
     * @param string $slug
     * @return unknown|boolean
     */
    public static function usernameExists( $username )
    {
        $username = static::usernameFromString( $username );
        
        $clone = (new static)->load(array('username'=>$username));
    
        if (!empty($clone->id)) {
            return $clone;
        }
    
        return false;
    }    
    
    /**
     * Strips whitespace and converts to lowercase
     * 
     * @param unknown $string
     * @return unknown
     */
    public static function usernameFromString( $string ) 
    {
        $username = \Dsc\System::instance()->inputfilter->clean($string, 'ALNUM');
        
        return $username;
    }
    
    /**
     * Send an email to this user to validate their email address
     * 
     * @return \Users\Models\Users
     */
    public function sendEmailValidatingEmailAddress()
    {
        \Base::instance()->set('user', $this);
        
        $html = \Dsc\System::instance()->get( 'theme' )->renderView( 'Users/Views::emails_html/validation.php' );
        $text = \Dsc\System::instance()->get( 'theme' )->renderView( 'Users/Views::emails_text/validation.php' );
        $subject = 'Please verify your email address'; // TODO Add this to config?
        
        $this->__sendEmailValidatingEmailAddress = \Dsc\System::instance()->get('mailer')->send($this->email, $subject, array($html, $text) );
        
        return $this;
    }
    
    /**
     * Send an email to this user to reset their password
     *
     * @return \Users\Models\Users
     */
    public function sendEmailResetPassword()
    {
        \Base::instance()->set('user', $this);
    
        $html = \Dsc\System::instance()->get( 'theme' )->renderView( 'Users/Views::emails_html/password_reset_request.php' );
        $text = \Dsc\System::instance()->get( 'theme' )->renderView( 'Users/Views::emails_text/password_reset_request.php' );
        $subject = 'Password reset request'; // TODO Add this to config?
    
        $this->__sendEmailResetPassword = \Dsc\System::instance()->get('mailer')->send($this->email, $subject, array($html, $text) );
    
        return $this;
    }
    
    /**
     * Send an email to this user to let them know their password has been reset
     *
     * @return \Users\Models\Users
     */
    public function sendEmailPasswordResetNotification()
    {
        \Base::instance()->set('user', $this);
    
        $html = \Dsc\System::instance()->get( 'theme' )->renderView( 'Users/Views::emails_html/password_reset_notification.php' );
        $text = \Dsc\System::instance()->get( 'theme' )->renderView( 'Users/Views::emails_text/password_reset_notification.php' );
        $subject = 'Password reset notification'; // TODO Add this to config?
    
        $this->__sendEmailPasswordResetNotification = \Dsc\System::instance()->get('mailer')->send($this->email, $subject, array($html, $text) );
    
        return $this;
    }
    
    /**
     * Send an email to this user to verify ownership of a new email address 
     *
     * @return \Users\Models\Users
     */
    public function sendEmailChangeEmailConfirmation()
    {
        \Base::instance()->set('user', $this);
        
        $new_email = $this->{'change_email.email'}; 
    
        $html = \Dsc\System::instance()->get( 'theme' )->renderView( 'Users/Views::emails_html/verify_change_email.php' );
        $text = \Dsc\System::instance()->get( 'theme' )->renderView( 'Users/Views::emails_text/verify_change_email.php' );
        $subject = 'Please verify your email address'; // TODO Add this to config?
    
        $this->__sendEmailPasswordResetNotification = \Dsc\System::instance()->get('mailer')->send($new_email, $subject, array($html, $text) );
    
        return $this;
    }
    
    /**
     * Returns link to user's profile picture
     * 
     * @return	Either link to the image, or an empty string
     */
    public function profilePicture()
    {
    	$img = null;
    	
    	$networks = (array) $this->{'social'};
    	foreach ($networks as $network)
    	{
    	    if ($photo_url = \Dsc\ArrayHelper::get($network, 'profile.photoURL')) 
    	    {
    	        $img = $photo_url;
    	        break;
    	    }
    	}
    	
    	return $img;
    }
    
    /**
     * Get a customer's groups, ordered by group.ordering (highest first)
     * 
     * @return array 
     */
    public function groups()
    {
        $group_ids = array();
        foreach ($this->groups as $group)
        {
            if ($id = \Dsc\ArrayHelper::get($group, 'id'))
            {
                $group_ids[] = $id;
            }
        }
    
        if (empty($group_ids))
        {
            return array();
        }
    
        $list = (new \Users\Models\Groups)->setState('filter.ids', $group_ids)->setState('list.sort', array(
            'ordering' => 1
        ))->getItems();
    
        return $list;
    }
    
    /**
     * Adds the user to a group.
     * 
     * @param unknown $id
     */
    public function addToGroups(array $ids, $save=true)
    {
        foreach ($ids as $id) 
        {
            $this->groups[] = (string) $id;
        }        
        
        if ($save) {
            return $this->save();
        }
        
        return $this;
    }
    
    /**
     * Remove the user from a group.
     *
     * @param unknown $id
     */
    public function removeFromGroups(array $ids, $save=true)
    {
        $found = false;
        
        foreach ($ids as $id) 
        {
            foreach ($this->groups as $key=>$group)
            {
                if ($group_id = \Dsc\ArrayHelper::get($group, 'id'))
                {
                    if ((string) $id == (string) $group_id)
                    {
                        unset($this->groups[$key]);
                        $found = true;
                    }
                }
            }        	
        }
   
        if ($found && $save) {
            return $this->save();        
        }
        
        return $this;
    }
    
    /**
     * 
     * @return \Users\Models\Users
     */
    public function setLastVisit()
    {
        $this->set('last_visit', \Dsc\Mongo\Metastamp::getDate('now') );
        
        return $this->save(); 
    }

    /**
     * Validates a token, usually from clicking on a link in an email
     * and activates the resulting user
     * 
     * @throws \Exception
     */
    public static function validateLoginToken( $token )
    {
   		$user = (new static)->setState('filter.id', $token)->getItem();
   		if (empty($user->id) || $token != (string) $user->id)
   		{
   			throw new \Exception( 'Invalid Token' );
   		}
   		
   		$user->active = true;
   		
   		return $user->save();
    }
    
	/**
	 * Creates the user
	 * (target for the register form)
	 * 
	 * @throws \Exception
	 * 
	 * @return	\Users\Models\Users
	 */
	public static function createNewUser($data, $registration_action=null )
	{
		$user = (new static)->bind($data);
	
		// Check if the email already exists and give a custom message if so
		if (!empty($user->email) && $existing = $user->emailExists( $user->email ))
		{
			if ((empty($user->id) || $user->id != $existing->id))
			{
				throw new \Exception( 'This email is already registered', static::E_EMAIL_EXISTS );
			}
		}
	
		if (empty($registration_action))
		{
			$registration_action = \Users\Models\Settings::fetch()->{'general.registration.action'};
		}
        
		// $user->save() will handle other validations, such as username uniqueness, etc
		// and throws an exception if validation/save fails		
		switch ($registration_action)
		{
		    case "none":
		        $user->save();
		        break;		    
			case "auto_login":
				$user->active = true;
				$user->save();
        		\Dsc\System::instance()->get( 'auth' )->login( $user );
				
        		break;
			case "auto_login_with_validation":
				$user->active = false;
				$user->save();
				\Dsc\System::instance()->get( 'auth' )->login( $user );				
				$user->sendEmailValidatingEmailAddress();
								 
				break;
			default:
				$user->active = false;
				$user->save();
				$user->sendEmailValidatingEmailAddress();
								 
				break;
		}
		
		return $user;
	}
	
	public function unlinkedSocialProfiles()
	{
	    $settings = \Users\Models\Settings::fetch();
	    $providers = $settings->enabledSocialProviders();
	    
	    if (empty($this->social)) {
	    	return $providers;
	    }
	    
	    foreach ($this->social as $network=>$id) 
	    {
	        $key = array_search($network, $providers);
	        if ($key !== false) {
	            unset($providers[$key]);
	        }	    	
	    }
	    
	    return $providers;
	} 
	
	/**
	 *
	 * @param array $types
	 * @return unknown
	 */
	public static function distinctAdminTags($query=array())
	{
	    $model = new static();
	    $distinct = $model->collection()->distinct("admin_tags", $query);
	    $distinct = array_values( array_filter( $distinct ) );
	
	    return $distinct;
	}
	
	/**
	 * Converts this to a search item, used in the search template when displaying each search result
	 */
	public function toAdminSearchItem()
	{
	    $image = $this->profilePicture();
	
	    $item = new \Search\Models\Item(array(
	        'url' => './admin/user/edit/' . $this->id,
	        'title' => $this->fullName(),
	        'subtitle' => '',
	        'image' => $image,
	        'summary' => $this->email,
	        'datetime' => 'Last Visited: ' . date('Y-m-d', $this->{'last_visit.time'} )
	    ));
	
	    return $item;
	}	
}