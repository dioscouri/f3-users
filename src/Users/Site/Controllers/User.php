<?php
namespace Users\Site\Controllers;

class User extends Auth
{
    public function read()
    {
        $f3 = \Base::instance();
        
        $user = $this->getItem();
        $f3->set('user', $user);
        
        $view = \Dsc\System::instance()->get('theme');
        echo $view->render( 'Users/Site/Views::profile/read.php' );
    }
    
    public function readSelf()
    {
        $f3 = \Base::instance();
    
        $identity = $this->getIdentity();
        if (empty($identity->id)) 
        {
            $f3->reroute( '/login' );
            return;
        }
        
        if (!empty($identity->__safemode)) 
        {
        	$user = $identity;
        } 
            else 
        {
            $model = $this->getModel()->setState( 'filter.id', $identity->id );
            $user = $model->getItem();
        }

        $f3->set('user', $user);
            
        $view = \Dsc\System::instance()->get('theme');
        echo $view->render( 'Users/Site/Views::profile/readSelf.php' );
    }
    
    protected function getModel()
    {
        $model = new \Users\Models\Users;
        return $model;
    }
    
    protected function getItem()
    {
        $f3 = \Base::instance();
        $id = $this->inputfilter->clean( $f3->get( 'PARAMS.id' ), 'alnum' );
        $model = $this->getModel()->setState( 'filter.id', $id );
    
        try
        {
            $item = $model->getItem();
        }
        catch ( \Exception $e )
        {
            \Dsc\System::instance()->addMessage( "Invalid Item: " . $e->getMessage(), 'error' );
            $f3->reroute( '/' );
            return;
        }
    
        return $item;
    }
    
    /**
     * Displays the logged-in user's list of linked social profiles
     */
    public function socialProfiles()
    {
        $f3 = \Base::instance();
        
        $identity = $this->getIdentity();
        if (empty($identity->id))
        {
            $f3->reroute( '/login' );
            return;
        }
        
        if (!empty($identity->__safemode))
        {
            $user = $identity;
        }
        else
        {
            $model = $this->getModel()->setState( 'filter.id', $identity->id );
            $user = $model->getItem();
        }
        
        $f3->set('user', $user);
        
        $view = \Dsc\System::instance()->get('theme');
        echo $view->render( 'Users/Site/Views::social/profiles.php' );        
    }
    
    public function unlinkSocialProfile(){
    	$f3 = \Base::instance();
    	$provider = strtolower( $this->inputfilter->clean( $f3->get( 'PARAMS.provider' ), 'alnum' ) );
    	 
    	$identity = $this->getIdentity();
    	if (empty($identity->id))
    	{
    		$f3->reroute( '/login' );
    		return;
    	}
    	
    	if (!empty($identity->__safemode))
    	{
    		$user = $identity;
    	}
    	else
    	{
    		$model = $this->getModel()->setState( 'filter.id', $identity->id );
    		$user = $model->getItem();
    	}
    	
    	$user->clear( 'social.'.$provider );
    	$user->save();
    	$f3->reroute( '/user/social-profiles' );
    	return; 
    }
}
