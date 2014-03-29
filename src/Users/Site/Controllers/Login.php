<?php 
namespace Users\Site\Controllers;

class Login extends \Dsc\Controller 
{
    /**
     * Displays a dual login/register form
     */
    public function index($f3)
    {
        $identity = $this->getIdentity();
        if (!empty($identity->id))
        {
            $f3->reroute('/user');
        }
        
        $view = \Dsc\System::instance()->get('theme');
        echo $view->render( 'Users/Site/Views::login/dual.php' );        
    }
    
    /**
     * Displays just a login form
     */
    public function only($f3)
    {   

        $identity = $this->getIdentity();
        if (!empty($identity->id))
        {
            $f3->reroute('/user');
        }
        
        $view = \Dsc\System::instance()->get('theme');
        echo $view->render( 'Users/Site/Views::login/login.php' );
        

    }
    
    /**
     * Displays just a registration form
     */
    public function register($f3)
    {
        $identity = $this->getIdentity();
        if (!empty($identity->id))
        {
            $f3->reroute('/user');
        }
    
        $view = \Dsc\System::instance()->get('theme');
        echo $view->render( 'Users/Site/Views::login/register.php' );
    }
    
    /**
     * Performs logout
     */
    public function logout()
    {
        \Dsc\System::instance()->get('auth')->logout();
        \Base::instance()->reroute('/');
    }
    
    /**
     * Authenticates the user (performs the login)
     */
    public function auth()
    {
        /*
         * Let $this->auth->check() set the error, 
         * in case we want to pass social logins through this auth method
         *    
        $username_input = $this->input->getAlnum('login-username');
        $password_input = $this->input->getString('login-password');
        if (empty($username_input) || empty($password_input)) 
        {
            \Dsc\System::instance()->addMessage('Login failed - Incomplete Form', 'error');
            \Base::instance()->reroute("/login");
            return;
        }
        */
        
        $redirect = '/user';
        if ($custom_redirect = \Dsc\System::instance()->get('session')->get('site.login.redirect'))
        {
            $redirect = $custom_redirect;
        }
        
        $input = $this->input->getArray();
        
        try {
            $this->auth->check($input);
            
        } catch (\Exception $e) {
            \Dsc\System::addMessage('Login failed', 'error');
            \Dsc\System::addMessage($e->getMessage(), 'error');
            \Base::instance()->reroute("/login");
            return;
        }
        
        \Dsc\System::instance()->get('session')->set('site.login.redirect', null);
        \Base::instance()->reroute($redirect);
        
        return;            
    }
    
    /**
     * Creates the user
     * (target for the register form)
     */
    public function create()
    {
        
    }


     public function social() {

         try{
             \Hybrid_Endpoint::process();
          } catch( \Exception $e ){
            \Dsc\System::addMessage('Login failed', 'error');
            \Dsc\System::addMessage($e->getMessage(), 'error');
            \Base::instance()->reroute("/login");
          }
    }

    public function provider()
    {  
        $f3 = \Base::instance();
        $provider = $f3->get('PARAMS.provider');
        $hybridauth_config =  \Users\Models\Settings::fetch();
        $config = (array) $hybridauth_config->{'social'};
    
        try{
        // create an instance for Hybridauth with the configuration file path as parameter
            $hybridauth = new \Hybrid_Auth( $config );

        // try to authenticate the selected $provider
            $adapter = $hybridauth->authenticate( $provider );

        // grab the user profile
            $user_profile = $adapter->getUserProfile();



            $model = new \Users\Models\Users;
            $filter = 'social.'.$provider.'.identifier';

            $user = $model->setCondition($filter, $user_profile->identifier)->getItem();
     

       if(!empty($user->id)) {
	$this->auth->setIdentity($user );
		$f3->reroute('/user'); 
            }

           # here lets check if the user email we got from the provider already exists in our database ( for this example the email is UNIQUE for each user )
            // if authentication does not exist, but the email address returned  by the provider does exist in database, 
            // then authenticatewith the user having the address email in the database
               if ($user_profile->email) 
                {
                    // now check via email
                    try {
                        $model = new \Users\Models\Users;
                        $model->setState('filter.email', $user_profile->email);
                        if ($user = $model->getItem())
                        {   
                            $user->set('social.'.$provider.'identifier', $user_profile->identifier);
                            $user->set('social.'.$provider.'profile_url', $user_profile->profileURL);
                            $user->set('social.'.$provider.'website_url', $user_profile->webSiteURL);
                            $user->set('social.'.$provider.'display_name', $user_profile->displayName);
                            $user->set('social.'.$provider.'photo_url', $user_profile->photoURL);
                            
                            $user->save();
                            
                            $this->auth->setIdentity( $user );
        
                            $f3->reroute('/user');
                        }
                    } catch ( \Exception $e ) {
                        $this->setError('Invalid Email');
                    }
                }    


        # 4 - if authentication does not exist and email is not in use, then we create a new user 
          

            $data = array();
            $data['social'][$provider]['identifier'] = $user_profile->identifier;
            $data['social'][$provider]['profile_url'] = $user_profile->profileURL;
            $data['social'][$provider]['website_url'] = $user_profile->webSiteURL;
            $data['social'][$provider]['display_name'] = $user_profile->displayName;
            $data['social'][$provider]['photo_url'] = $user_profile->photoURL;

   
            $data['email'] = $user_profile->email;
            $data['first_name'] = $user_profile->firstName;
            $data['last_name'] = $user_profile->lastName;
                
            $password      = rand( ) ; # for the password we generate something random
            // 4.1 - create new user
         $model = new \Users\Models\Users;    
	$user = $model->create($data);    

            $this->auth->setIdentity( $user );
        
            $f3->reroute('/user');
        }
        catch( \Exception $e ){
            // Display the recived error
            if($f3->get('DEBUG')) {

                switch( $e->getCode() ){ 
                    case 0 : $error = "Unspecified error."; break;
                    case 1 : $error = "Hybriauth configuration error."; break;
                    case 2 : $error = "Provider not properly configured."; break;
                    case 3 : $error = "Unknown or disabled provider."; break;
                    case 4 : $error = "Missing provider application credentials."; break;
                    case 5 : $error = "Authentication failed. The user has canceled the authentication or the provider refused the connection."; break;
                    case 6 : $error = "User profile request failed. Most likely the user is not connected to the provider and he should to authenticate again."; 
                             $adapter->logout(); 
                             break;
                    case 7 : $error = "User not connected to the provider."; 
                             $adapter->logout(); 
                             break;
                } 

                // well, basically your should not display this to the end user, just give him a hint and move on..
                $error .= "<br /><br /><b>Original error message:</b> " . $e->getMessage(); 
                $error .= "<hr /><pre>Trace:<br />" . $e->getTraceAsString() . "</pre>"; 
            } else {

                $error = 'Something went wrong';
            } 

            $this->setError($error ); 
               
            $f3->reroute('/login');
        }
    }

}
