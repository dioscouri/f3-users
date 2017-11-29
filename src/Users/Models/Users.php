<?php
namespace Users\Models;

class Users extends \Dsc\Mongo\Collections\Taggable
{
    public $username;
    public $password;
    public $first_name;
    public $last_name;
    public $full_name;  // full user's name for searching and dispalying purposes
    public $email;              // if this is a guest account, store the fake email here
    public $role = null;
    public $active = true;
    public $banned = false;
    public $suspended = false;
    public $social = array();
    public $groups = array();
    public $photo;
    public $last_visit = array();
    public $admin_tags = array();
    public $birthday;   // YYYY-MM-DD
    public $guest = false;
    public $guest_email = null; // if this is a guest account, optionally store the real email here
    
    protected $__collection_name = 'users';
    protected $__type = 'users';
    
    protected $__config = array(
        'default_sort' => array(
            'last_visit.time' => -1
        )
    );
	
    protected $__enable_trash = true;
    
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
                'guest_email' => $key
            );            
            $where[] = array(
                'first_name' => $key
            );
            $where[] = array(
                'last_name' => $key
            );
            $where[] = array(
                'full_name' => $key
            );
            
            $this->setCondition('$or', $where);
        }
        
        $filter_username = $this->getState('filter.username', null, 'alnum');
        if (strlen($filter_username))
        {
            //$filter_username = strtolower($filter_username);
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
            $filter_email = strtolower($filter_email);
            $this->setCondition('email', $filter_email);
        }
        
        $filter_guest_email = $this->getState('filter.guest_email');
        if (strlen($filter_guest_email))
        {
            $filter_guest_email = strtolower($filter_guest_email);
            $this->setCondition('guest_email', $filter_guest_email);
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

        $filter_auto_login_token = $this->getState( 'filter.auto_login_token' );
        if (strlen($filter_auto_login_token))
        {
        	$this->setCondition('auto_login.token', (string) $filter_auto_login_token );
        }       
        
        $filter_admin_tags = (array) $this->getState('filter.admin_tags');
        if (!empty($filter_admin_tags))
        {
            $filter_admin_tags = array_filter( array_values( $filter_admin_tags ), function( $var ) {
                $var = trim($var);
                return !empty( $var ); 
            } );		
		
            
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
        
        $this->full_name = $this->fullName();
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
    
    public function getRole() {
    	return  (new \Users\Models\Roles)->setCondition('slug', $this->role)->getItem();
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
        
        if (isset($this->__groups) && empty($this->__groups)) 
        {
            $this->groups = array();
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
    public static function generateRandomString($length = 10, $url = false)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!';
        
        if($url == false) {
            $characters .= '@#&%$^*()';
        }
        
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
     * Get the account's email address for normal (marketing) communication.
     * If this is a guest account, the "fake" auto-generated email will be returned so no communication reaches the user.
     * If you want a guest account's real email address, send $guest=true as the function argument
     * 
     * @param bool $guest 
     * @return string
     */
    public function email($guest=false)
    {
        $email = $this->email;
        
        if ($guest && $this->guest && !empty($this->guest_email)) 
        {
            $email = $this->guest_email;
        }
        
        return $email;
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
    public static function guestEmailExists( $email )
    {
        $clone = (new static)->load(array('guest_email'=>$email));
    
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
        $email = $this->email;
        
        $mailer = \Dsc\System::instance()->get('mailer');
        if ($content = $mailer->getEmailContents('users.validate_email', array(
            'user' => $this
        ))) {
            $this->__sendEmailPasswordResetNotification = $mailer->send( $email, $content['subject'], $content['body'], $content['fromEmail'], $content['fromName'] );
        }
        
        return $this;
    }
    
    /**
     * Send an email to this user to reset their password
     *
     * @return \Users\Models\Users
     */
    public function sendEmailResetPassword()
    {
        $email = $this->email;
        
        $mailer = \Dsc\System::instance()->get('mailer');
        if ($content = $mailer->getEmailContents('users.password_reset_request', array(
            'user' => $this
        ))) {
            $this->__sendEmailPasswordResetNotification = $mailer->sendEvent( $email, $content);
        }
        
        return $this;
    }
    
    /**
     * Send an email to this user to let them know their password has been reset
     *
     * @return \Users\Models\Users
     */
    public function sendEmailPasswordResetNotification()
    {
        $email = $this->email;
        
        $mailer = \Dsc\System::instance()->get('mailer');
        if ($content = $mailer->getEmailContents('users.password_reset_notification', array(
            'user' => $this
        ))) {
            $this->__sendEmailPasswordResetNotification = $mailer->sendEvent( $email, $content);
        }
        
        return $this;
    }
    
    /**
     * Send an email to this user to verify ownership of a new email address 
     *
     * @return \Users\Models\Users
     */
    public function sendEmailChangeEmailConfirmation()
    {
        $email = $this->{'change_email.email'};
        
        $mailer = \Dsc\System::instance()->get('mailer');
        if ($content = $mailer->getEmailContents('users.verify_change_email', array(
            'user' => $this,
            'link' => \Dsc\Url::base() . 'user/change-email/confirm?new_email=' . urlencode( $this->{'change_email.email'} ) . '&token=' . $this->{'change_email.token'},
            'token' => $this->{'change_email.token'}
        ))) {
            $this->__sendEmailPasswordResetNotification = $mailer->sendEvent( $email, $content);
        }
        
        return $this;
    }
    
    /**
     * Returns link to user's profile picture
     * 
     * @return	Either link to the image, or an empty string
     */
    public function profilePicture($img = null)
    {
    	if($this->{'avatar.slug'}) {
    		
    		return '/asset/' . $this->{'avatar.slug'};
    	}
    	
    	
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
     * Is the user in the requested group
     *
     * @return bool
     */
    public function inGroup($id, $id_type='slug')
    {
        foreach ($this->groups as $group)
        {
            if ((string) \Dsc\ArrayHelper::get($group, $id_type) == (string) $id) 
            {
                return true;
            }
        }

        return false;
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
     * Adds the user to a group.
     *
     * @param unknown $id
     */
    public function addToGroupsBySlugs(array $slugs, $save=true)
    {
        $ids = array();
        foreach ($slugs as $slug)
        {
            $group = (new \Users\Models\Groups())->setState('filter.slug', $slug)->getItem();
            if (!empty($group->id))
            {
                $ids[] = $group->id;
            }
        }
        
        if (empty($ids)) 
        {
            return $this;
        }
    
        return $this->addToGroups($ids, $save);
    }
    
    /**
     * Remove the user from a group.
     *
     * @param unknown $id
     */
    public function removeFromGroupsBySlugs(array $slugs, $save=true)
    {
        $ids = array();
        foreach ($slugs as $slug)
        {
            $group = (new \Users\Models\Groups())->setState('filter.slug', $slug)->getItem();
            if (!empty($group->id))
            {
                $ids[] = $group->id;
            }
        }
        
        if (empty($ids))
        {
            return $this;
        }
        
        return $this->removeFromGroups($ids, $save);
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
	        $key = array_search(strtolower($network), $providers);
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
	    $distinct = $model->collection()->distinct("admin_tags", $query ? $query : null);
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
	        'subtitle' => $this->email,
	        'image' => $image,
	        'summary' => $this->username,
	        'datetime' => 'Last Visited: ' . date('Y-m-d', $this->{'last_visit.time'} )
	    ));
	
	    return $item;
	}	
}
