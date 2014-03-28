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
            $filter = 'social.'.$provider;
            //$model->setState($filter, $user_profile->identifier)->getItem();
        # 1 - check if user already have authenticated using this provider before
        //    $authentication_info = $authentication->find_by_provider_uid( $provider, $user_profile->identifier );

        # 2 - if authentication exists in the database, then we set the user as connected and redirect him to his profile page
        /*    if( $authentication_info ){
                // 2.1 - store user_id in session
                $_SESSION["user"] = $authentication_info["user_id"]; 

                // 2.2 - redirect to user/profile
                $this->redirect( "users/profile" );
            }

        # 3 - else, here lets check if the user email we got from the provider already exists in our database ( for this example the email is UNIQUE for each user )
            // if authentication does not exist, but the email address returned  by the provider does exist in database, 
            // then we tell the user that the email  is already in use 
            // but, its up to you if you want to associate the authentication with the user having the adresse email in the database
            if( $user_profile->email ){
                $user_info = $user->find_by_email( $user_profile->email );

                if( $user_info ) {
                    die( '<br /><b style="color:red">Well! the email returned by the provider ('. $user_profile->email .') already exist in our database, so in this case you might use the <a href="index.php?route=users/login">Sign-in</a> to login using your email and password.</b>' );
                }
            }
 */
        # 4 - if authentication does not exist and email is not in use, then we create a new user 
          

            $data = array();
            $data['social'][$provider]['identifier'] = $user_profile->identifier;
           $data['social'][$provider]['profile_url'] = $user_profile->profileURL;
	    $data['social'][$provider]['display_name'] = $user_profile->displayName;

	    $data['email'] = $user_profile->email;
            $data['first_name'] = $user_profile->firstName;
            $data['last_name'] = $user_profile->lastName;
            $data['display_name'] = $user_profile->displayName;
            $data['website_url'] = $user_profile->webSiteURL;
        
            $password      = rand( ) ; # for the password we generate something random




            // 4.1 - create new user
            $doc = $model->create($data);    



            // 4.2 - creat a new authentication for him
            //$authentication->create( $new_user_id, $provider, $provider_uid, $email, $display_name, $first_name, $last_name, $profile_url, $website_url );
 
            // 4.3 - store the new user_id in session
             $f3->set('SESSION.user', $doc);
            // 4.4 - redirect to user/profile


             $f3->reroute('/welcome');
        }
        catch( \Exception $e ){
            // Display the recived error
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

            // load error view
            $data = array( "error" => $error ); 
            var_dump($data); die();
        }
    }

}
