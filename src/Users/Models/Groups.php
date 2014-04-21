<?php 
namespace Users\Models;

class Groups extends \Dsc\Mongo\Collection 
{
    use \Dsc\Traits\Models\OrderableCollection;
    
    /**
     * Default Document Structure
     * @var unknown
     */
	public $_id;
	public $name;
    
    protected $__collection_name = 'users.groups';
    protected $__config = array(
        'default_sort' => array(
            'name' => 1
        )        
    );
    
    protected function fetchConditions()
    {   
        parent::fetchConditions();
    
        $filter_keyword = $this->getState('filter.keyword');
        if ($filter_keyword && is_string($filter_keyword))
        {
            $key =  new \MongoRegex('/'. $filter_keyword .'/i');
            
            $where = array();
            $where[] = array('name'=>$key);
            
            $this->setCondition('$or', $where);
        }
    
        $filter_id = $this->getState('filter.id');
        if (strlen($filter_id))
        {
            $this->setCondition('_id', new \MongoId((string) $filter_id));
        }
        
        $filter_name_contains = $this->getState('filter.name-contains', null, 'name');
        if (strlen($filter_name_contains))
        {
            $key =  new \MongoRegex('/'. $filter_name_contains .'/i');
            $this->setCondition('name', $key);
        }
    
        return $this;
    }
    
    protected function beforeCreate()
    {
        if (empty($this->ordering)) 
        {
            $this->ordering = $this->nextOrdering();
        }
        
        return parent::beforeCreate();
    }
    
    protected function afterSave()
    {
    	$this->compressOrdering();
    }
    
    protected function afterDelete()
    {
        $this->compressOrdering();
    }
}