<?php 
class UsersBootstrap extends \Dsc\BaseBootstrap{
	protected $dir = __DIR__;
	protected $namespace = 'Users';
}
$app = new UsersBootstrap();