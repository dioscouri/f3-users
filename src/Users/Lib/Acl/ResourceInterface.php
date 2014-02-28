<?php 
namespace Users\Lib\Acl;

interface ResourceInterface 
{
    /**
     * 
     */
    public function __construct ($name, $description=null);
    
    /**
     * Returns the role name
     */
    public function getName();
    
    /**
     * Returns role description
     */
    public function getDescription();
    
    /**
     * Magic method __toString 
     */
    public function __toString();
}
?>