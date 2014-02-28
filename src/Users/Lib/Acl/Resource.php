<?php 
namespace Users\Lib\Acl;

class Resource implements ResourceInterface 
{
    protected $name=null;
    protected $description=null;
    
    /**
     * 
     */
    public function __construct($name, $description=null)
    {
    	$this->name=$name;
    	$this->description=$description;
    }
    
    /**
     * Returns the role name
     */
    public function getName()
    {
    	return $this->name;
    }
    
    /**
     * Returns role description
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Magic method __toString 
     */
    public function __toString() 
    {
    	return $this->getName();
    }
}
?>