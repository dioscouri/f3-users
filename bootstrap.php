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
        parent::preSite();
        
        if (class_exists('\Minify\Factory'))
        {
            \Minify\Factory::registerPath($this->dir . "/src/");
        }
        
        static::setActive();
    }

    protected function preAdmin()
    {
        parent::preAdmin();
        
        if (class_exists('\Search\Factory'))
        {
            \Search\Factory::registerSource(new \Search\Models\Source(array(
                'id' => 'users',
                'title' => 'Users',
                'class' => '\Users\Models\Users',
                'priority' => 20,
            )));
        }
        
        static::setActive();
    }
    
    public static function setActive()
    {
        if (!\Audit::instance()->isbot())
        {
            if (class_exists('\Activity\Models\Actors')) 
            {
                $actor = \Activity\Models\Actors::fetch();
                if ($actor->isExcluded()) 
                {
                    return;
                }
            }
            
            if (\Dsc\System::instance()->get('input')->get('ping', null, 'int') != 1)
            {
                $fw = \Base::instance();
                $path = $fw->hive()['PATH'];
                switch ($path) 
                {
                    // ignore certain paths, even if they aren't specifically pings
                    case "/minify/css":
                    case "/minify/js":
                        break;
                    default:
                        (new \Dsc\Mongo\Collections\Sessions)->store();
                        break;
                }
            }            
        }
        
        \Dsc\Mongo\Collections\Sessions::throttledCleanup();
    }    
}
$app = new UsersBootstrap();