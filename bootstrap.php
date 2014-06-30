<?php
class UsersBootstrap extends \Dsc\Bootstrap
{
    protected $dir = __DIR__;

    protected $namespace = 'Users';

    protected function runSite()
    {
        parent::runSite();
        
        \Dsc\System::instance()->get('router')->mount(new \Users\Site\Routes\Prefixed(), $this->namespace);
    }

    protected function preSite()
    {
        if (class_exists('\Minify\Factory'))
        {
            \Minify\Factory::registerPath($this->dir . "/src/");
        }
    }

    protected function preAdmin()
    {
        if (class_exists('\Search\Factory'))
        {
            \Search\Factory::registerSource(new \Search\Models\Source(array(
                'id' => 'users',
                'title' => 'Users',
                'class' => '\Users\Models\Users',
                'priority' => 20,
            )));
        }
    }
}
$app = new UsersBootstrap();