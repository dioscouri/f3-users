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
        
        if (empty($this->username))
        {
            $this->username = $this->email;
        }
        
        $this->username = static::usernameFromString( $this->username );
        if (empty($this->username))
        {
            $this->setError('Username is required');
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
}