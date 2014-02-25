<?php 
namespace Users\Admin\Models;

use Joomla\Crypt\Password;

class Users extends \Dsc\Mongo\Collection 
{
	/**
	 * Document Structure
	 * @var unknown
	 */
	public $_id;
	public $username;
	public $first_name;
	public $last_name;
	public $email;
	
	protected $__collection_name = 'users';
	protected $__default_options = array(
        'append' => true
	);
	
    protected function fetchConditions()
    {   
        parent::fetchConditions();
        
        $filter_keyword = $this->getState('filter.keyword');
        if ($filter_keyword && is_string($filter_keyword))
        {
            $key =  new \MongoRegex('/'. $filter_keyword .'/i');
    
            $where = array();
            $where[] = array('username'=>$key);
            $where[] = array('email'=>$key);
            $where[] = array('first_name'=>$key);
            $where[] = array('last_name'=>$key);
    
            $this->setCondition('$or', $where);
        }
    
        $filter_id = $this->getState('filter.id');
        if (strlen($filter_id))
        {
            $this->setCondition('_id', new \MongoId((string) $filter_id));
        }
        
        $filter_username = $this->getState('filter.username', null, 'alnum');
        if (strlen($filter_username))
        {
            $this->setCondition('username', $filter_username);
        }
        
        $filter_username_contains = $this->getState('filter.username-contains', null, 'alnum');
        if (strlen($filter_username_contains))
        {
            $key =  new \MongoRegex('/'. $filter_username_contains .'/i');
            $this->setCondition('username', $key);
        }
        
        $filter_email_contains = $this->getState('filter.email-contains');
        if (strlen($filter_email_contains))
        {
            $key =  new \MongoRegex('/'. $filter_email_contains .'/i');
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
            $this->setCondition('groups.id', new \MongoId((string) $filter_group) );
        }
    
        return $this;
    }

    public function OLDvalidate( $values, $options=array(), $mapper=null )
    {
        if (empty($values['email'])) {
            $this->setError('Email is required');
        }
        
        if (empty($values['password'])) {
            $this->setError('Password is required');
        }

        return parent::validate( $values, $options );
    }

    public function OLDcreate( $values, $options=array() )
    {
        if (empty($values['password'])) {
            $this->auto_password = $this->generateRandomString( 10 ); // save this for later emailing to the user, if necessary
            $values['password'] = (new \Joomla\Crypt\Password\Simple)->create( $this->auto_password );
        }
                
        return $this->save( $values, $options );
    }
    
    public function OLDupdate( $mapper, $values, $options=array() )
    {
        if (!empty($values['new_password'])) 
        {
            if (empty($values['confirm_new_password']))
            {
                $this->setError('Must confirm new password');
            }
            
            if ($values['new_password'] != $values['confirm_new_password'])
            {
                $this->setError('New password and confirmation value do not match');
            }

            $values['password'] = (new \Joomla\Crypt\Password\Simple)->create( $values['new_password'] );
        }
            else 
        {
            $values['password'] = $mapper->password;
        }

        unset($values['new_password']);
        unset($values['confirm_new_password']);
        
        return $this->save( $values, $options, $mapper );
    }
    
    public function OLDsave( $values, $options=array(), $mapper=null )
    {
        if (empty($options['skip_validation']))
        {
            $this->validate( $values, $options, $mapper );
        }
        
        if (empty($values['username'])) {
            $values['username'] = $values['email'];
        }
        
        $values['username'] = $this->inputfilter->clean( $values['username'], 'ALNUM' );
        
        if (!empty($values['groups'])) 
        {
            $groups = array();
            foreach ($values['groups'] as $key => $id) 
            {
                $item = (new \Users\Admin\Models\Groups)->setState('filter.id', $id)->getItem();
                $groups[] = array("id" =>  $item->id, "name" => $item->name);
        
            }
            $values['groups'] = $groups;
        }        
        
        $options['skip_validation'] = true; // we've already done it above, so stop the parent from doing it
    
        return parent::save( $values, $options, $mapper );
    }

    /**
     * Generates a random password string
     * @param number $length
     * @return string
     */
    function generateRandomString( $length=10 ) 
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $randomString;
    }
}