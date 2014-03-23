<?php
class UsersBootstrap extends \Dsc\Bootstrap
{
    protected $dir = __DIR__;
    protected $namespace = 'Users';

    protected function runSite()
    {
        parent::runSite();
        
        \Dsc\System::instance()->get('router')->mount( new \Users\Site\Routes\Prefixed, $this->namespace );
    }
}
$app = new UsersBootstrap();