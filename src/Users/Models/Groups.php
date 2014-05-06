<?php 
namespace Users\Models;

class Groups extends \Dsc\Mongo\Collections\Describable 
{
    use \Dsc\Traits\Models\OrderableCollection;
    
    protected $__collection_name = 'users.groups';
    protected $__type = 'users.groups';
    
    protected $__config = array(
        'default_sort' => array(
            'title' => 1
        )        
    );
    
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