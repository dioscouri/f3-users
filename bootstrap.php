<?php 
class UsersBootstrap extends \Dsc\Bootstrap{
	protected $dir = __DIR__;
	protected $namespace = 'Users';
}
$app = new UsersBootstrap();