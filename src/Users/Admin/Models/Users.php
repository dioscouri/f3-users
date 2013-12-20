<?php 
namespace Users\Admin\Models;

use Joomla\Crypt\Password;

class Users extends \Dsc\Models\Db\Mongo 
{
	protected $collection = 'users';
    protected $default_ordering_direction = '1';
    protected $default_ordering_field = 'username';
    
    public function __construct($config=array())
    {
        $config['filter_fields'] = array(
            'username', 'email', 'first_name', 'last_name'
        );
        $config['order_directions'] = array('1', '-1');
        
        parent::__construct($config);
    }
    
    protected function fetchFilters()
    {   
        var_dump($this->filters);
       
        $filter_keyword = $this->getState('filter.keyword');
        if ($filter_keyword && is_string($filter_keyword))
        {
            $key =  new \MongoRegex('/'. $filter_keyword .'/i');
    
            $where = array();
            $where[] = array('username'=>$key);
            $where[] = array('email'=>$key);
            $where[] = array('first_name'=>$key);
            $where[] = array('last_name'=>$key);
    
            $this->filters['$or'] = $where;
        }
    
        $filter_id = $this->getState('filter.id');
        if (strlen($filter_id))
        {
            $this->filters['_id'] = new \MongoId((string) $filter_id);
        }
        
        $filter_username_contains = $this->getState('filter.username-contains', null, 'username');
        if (strlen($filter_username_contains))
        {
            $key =  new \MongoRegex('/'. $filter_username_contains .'/i');
            $this->filters['username'] = $key;
        }
        
        $filter_email_contains = $this->getState('filter.email-contains');
        if (strlen($filter_email_contains))
        {
            $key =  new \MongoRegex('/'. $filter_email_contains .'/i');
            $this->filters['email'] = $key;
        }
       

        $filter_password = $this->getState('filter.password');
        if (strlen($filter_password))
        {
            $this->filters['password'] = $filter_password;
        }

        $filter_group = $this->getState('filter.group');

        if (strlen($filter_group))
        {
            $this->filters['groups.id'] = new \MongoId((string) $filter_group);
        }
    
        return $this->filters;
    }
    



    protected function buildOrderClause()
    {
        $order = null;
    
        if ($this->getState('order_clause')) {
            return $this->getState('order_clause');
        }
    
        if ($this->getState('list.order') && in_array($this->getState('list.order'), $this->filter_fields)) {
    
            $direction = '1';
            if ($this->getState('list.direction') && in_array($this->getState('list.direction'), $this->order_directions)) {
                $direction = (int) $this->getState('list.direction');
            }
    
            $order = array( $this->getState('list.order') => $direction);
        }
    
        return $order;
    }


    public function save( $values, $options=array(), $mapper=null )
    {   

        

        if (empty($options['skip_validation']))
        {
            $this->validate( $values, $options, $mapper );
        }
        
        $key = strtolower( get_class() ) . "." . microtime(true);
        $key = $this->inputfilter->clean($key, 'ALNUM');
        $f3 = \Base::instance();
        $f3->set($key, $values);
        
        // bind the mapper to the values array
        if (empty($mapper)) {
            $mapper = $this->getMapper();
        }
        $mapper->copyFrom( $key );
        $f3->clear($key);
         
        //get the groups model, so down the line we can add more information to the document about the group if need
        if($values['groups']) {
            $groups = array();
            foreach ($values['groups'] as $key => $id) {
                $model = new \Users\Admin\Models\Groups;
                $model->setState('filter.id', $id);
                $item =  $model->getItem();
                $groups[] = array("id" =>  $item->_id, "name" => $item->name);
                
            }
            $mapper->groups = $groups;
         }




        // do the save
        try {
            $mapper->save();
        } catch (\Exception $e) {
            $this->setError( $e->getMessage() );
            return $this->checkErrors();
        }
        
        return $mapper;
    }

}