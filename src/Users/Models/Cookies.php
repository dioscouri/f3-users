<?php
namespace Users\Models;

class Cookies extends \Dsc\Mongo\Collection
{
	protected $__collection_name = 'users.cookies';
	
	
	
	
	protected function beforeSave() {
		//set the created date to allow auto deleting
		//http://docs.mongodb.org/manual/tutorial/expire-data/
		$this->createdAt = new \MongoDate();
		
		parent::beforeSave();
	}
	
}