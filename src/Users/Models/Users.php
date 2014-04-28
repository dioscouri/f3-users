<?php
namespace Users\Models;

class Users extends \Dsc\Mongo\Collection
{
    /**
     * Default Document Structure
     * 
     * @var unknown
     */
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
    
    protected $__collection_name = 'users';

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
            $this->setCondition('forgot_password.token', new \MongoId((string) $filter_forgot_password_token));
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
        if (! empty($this->groups))
        {
            $groups = array();
            foreach ($this->groups as $key => $id)
            {
                if (is_array($id))
                {
                    $groups[] = $id;
                }
                elseif (is_string($id))
                {
                    $item = (new \Users\Models\Groups())->setState('filter.id', $id)->getItem();
                    $groups[] = array(
                        "id" => $item->id,
                        "name" => $item->name
                    );
                }
            }
            $this->groups = $groups;
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
     * Returns link to user's profile picture
     * 
     * @return	Either link to the image, or an empty string
     */
    public function getProfilePicture()
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
}