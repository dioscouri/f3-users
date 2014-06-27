<?php
namespace Users\Lib;

use Users\Models\Users;
use Users\Models\RememberTokens;
use Users\Models\SuccessLogins;
use Users\Models\FailedLogins;

/**
 * Users\Lib\Auth
 * Authentication/Identity Management
 */
class Auth extends \Dsc\Singleton
{
    /**
     * Checks the user credentials
     *
     * @param array $credentials
     * @return boolan
     */
    public function check($credentials)
    {
        $identity = null;
        
        $username_input = !empty($credentials['login-username']) ? $this->inputfilter->clean( $credentials['login-username'] ) : null;
        $password_input = !empty($credentials['login-password']) ? $this->inputfilter->clean( $credentials['login-password'] ) : null;
        
        // check if safemode is being used
        $safemode_enabled = \Base::instance()->get('safemode.enabled');
        $safemode_user = \Base::instance()->get('safemode.username');
        $safemode_email = \Base::instance()->get('safemode.email');
        $safemode_password = \Base::instance()->get('safemode.password');
        $safemode_id = \Base::instance()->get('safemode.id');
        
        $regex = '/^[0-9a-z]{24}$/';

        if (preg_match($regex, (string) $safemode_id))
        {
            $safemode_id = new \MongoId($safemode_id);
        }
        else
        {
            $safemode_id = new \MongoId();
        }        
        
        if ($safemode_enabled && ($username_input === $safemode_user || $username_input === $safemode_email))
        {

            if (password_verify($password_input, $safemode_password))
            {

                //Load safemode user from collection
                $user = (new \Users\Models\Users)->setCondition('email',$safemode_email)->getItem();
                if (empty($user->id))
                {
                    $user = new \Users\Models\Users;
                    $user->id = $safemode_id; 
                }
                $user->username = $safemode_user;
                $user->first_name = $safemode_user;
                $user->password = $safemode_password;
                $user->email = $safemode_email;
                $role = \Base::instance()->get('safemode.role');
                if (!$role) {
                    $role = 'root';
                }
                $user->role = $role;
                $user->__safemode = true;  
                
        
                $this->setIdentity( $user );
                $identity = $user;

            }
        }

        if (!$identity && !empty($username_input)) 
        {
            // now check standard login via username
            try {
                $model = new \Users\Models\Users;
                $model->setState('filter.username', $username_input);
                if ($itemByUsername = $model->getItem())
                {
                    if (password_verify($password_input, $itemByUsername->password))
                    {
                        $this->setIdentity( $itemByUsername );
                        $identity = $itemByUsername;
                    }
                }
            } catch ( \Exception $e ) {
                $this->setError('Invalid Username');
            }
        }
        
        if (!$identity && !empty($username_input)) 
        {
            // now check via email
            try {
                $model = new \Users\Models\Users;
                $model->setState('filter.email', $username_input);
                if ($itemByEmail = $model->getItem())
                {
                    if (password_verify($password_input, $itemByEmail->password))
                    {
                        $this->setIdentity( $itemByEmail );
                        $identity = $itemByEmail;
                    }
                }
            } catch ( \Exception $e ) {
                $this->setError('Invalid Email');
            }
        }

        // If auth has not happened already, trigger Authentication Listeners
        if (!$identity) 
        {
        	// they are responsible for setting the identity with ->setIdentity( $identity );
            $event = new \Joomla\Event\Event( 'onUserAuthentication' );
            $event->addArgument('credentials', $credentials);
            \Dsc\System::instance()->getDispatcher()->triggerEvent($event);        	
        }
        
        // after triggering Auth Listeners, check if identity has been set.
        $identity = $this->getIdentity();

        if (!empty($identity->id))
        {
            // Check if the user was flagged
            $this->checkUserFlags( $identity );
                        
            // If so, login has been successful, so trigger Login Listeners
            $this->login( $identity );
            
            return true;
        }
        
        // otherwise, login failed
        throw new \Exception('Invalid login credentials.  Please try again.');
        
        /*
        // Check if the user exists
        $user = \Users\Models\Users::findFirst(array(
        	array('email'=>$credentials['email'])
        ));
        if ($user == false) {
            $this->registerUserThrottling(0);
            throw new \Exception('Wrong email/password combination');
        }

        // Check the password
        if (!$this->security->checkHash($credentials['password'], $user->password)) {
            $this->registerUserThrottling($user->id);
            throw new \Exception('Wrong email/password combination');
        }

        // Check if the user was flagged
        $this->checkUserFlags($user);

        // Register the successful login
        $this->saveSuccessLogin($user);

        // Check if the remember me was selected
        if (isset($credentials['remember'])) {
            $this->createRememberEnviroment($user);
        }

        */
    }
    
    /**
     * Triggers Listeners observing the afterUserLogin event
     * 
     * @param unknown $identity
     */
    public function login( \Users\Models\Users $user )
    {
        $this->setIdentity($user);
        
        try {
            $user->setLastVisit();
        } catch (\Exception $e) {
        }
       
        
        $event = new \Joomla\Event\Event( 'afterUserLogin' );
        $event->addArgument('identity', $user);
        
        return \Dsc\System::instance()->getDispatcher()->triggerEvent($event);
    }
    
    /**
     * Perform logout actions (e.g. trigger Listeners, etc)
     * and logout the user
     * 
     */
    public function logout($destroy=false)
    {
        $global_app_name = \Base::instance()->get('APP_NAME');
        $identity = $this->getIdentity();
        
        // Trigger plugin event for before logout
        $event = new \Joomla\Event\Event( 'beforeUserLogout' );
        $event->addArgument('identity', $identity)->addArgument('global_app', $global_app_name);
        \Dsc\System::instance()->getDispatcher()->triggerEvent($event);

        // actually logout the user
        if (!empty($destroy)) {
            // completely kill the session
            \Dsc\System::instance()->get('session')->destroy();
        } else {
            $this->remove();
            \Dsc\System::instance()->get('session')->removeAppSpace();
        }
        
        // Trigger plugin event for after logout
        $event = new \Joomla\Event\Event( 'afterUserLogout' );
        $event->addArgument('identity', $identity)->addArgument('global_app', $global_app_name);
        \Dsc\System::instance()->getDispatcher()->triggerEvent($event);
    }

    /**
     * Creates the remember me environment settings the related cookies and generating tokens
     *
     * @param Users\Models\Users $user
     */
    public function saveSuccessLogin($user)
    {
        $successLogin = new SuccessLogins();
        $successLogin->usersId = $user->id;
        $successLogin->ipAddress = $this->request->getClientAddress();
        $successLogin->userAgent = $this->request->getUserAgent();
        if (!$successLogin->save()) {
            $messages = $successLogin->getMessages();
            throw new \Exception($messages[0]);
        }
    }

    /**
     * Implements login throttling
     * Reduces the efectiveness of brute force attacks
     *
     * @param int $userId
     */
    public function registerUserThrottling($userId)
    {
        $failedLogin = new FailedLogins();
        $failedLogin->usersId = $userId;
        $failedLogin->ipAddress = $this->request->getClientAddress();
        $failedLogin->attempted = time();
        $failedLogin->save();

        $attempts = FailedLogins::count(array(
            'ipAddress = ?0 AND attempted >= ?1',
            'bind' => array(
                $this->request->getClientAddress(),
                time() - 3600 * 6
            )
        ));

        switch ($attempts) {
            case 1:
            case 2:
                // no delay
                break;
            case 3:
            case 4:
                sleep(2);
                break;
            default:
                sleep(4);
                break;
        }
    }

    /**
     * Creates the remember me environment settings the related cookies and generating tokens
     *
     * @param Users\Models\Users $user
     */
    public function createRememberEnviroment(\Users\Models\Users $user)
    {

    }

    /**
     * Check if the session has a remember me cookie
     *
     * @return boolean
     */
    public function hasRememberMe()
    {
        // TODO is RMM in the cookie?
        return false;
    }

    /**
     * Logs on using the information in the coookies
     *
     * @return
     */
    public function loginWithRememberMe()
    {
        // TODO try a login from cookie data
    }

    /**
     * Checks if the user is banned/inactive/suspended
     *
     * @param Users\Models\Users $user
     */
    public function checkUserFlags(\Users\Models\Users $user)
    {
        if (empty($user->active)) {
            $user->sendEmailValidatingEmailAddress();
            $this->remove();
            throw new \Exception("You haven't verified your email address yet.  Please check your email for further instructions.");
        }
        
        if (!empty($user->banned)) {
            $this->remove();
            throw new \Exception('The user is banned');
        }
        
        if (!empty($user->suspended)) {
            $this->remove();
            throw new \Exception('The user is suspended');
        }
    }

    /**
     * Returns the current logged in user's Users object.
     * Returns an empty Users object if no logged in user.
     * 
     * @return \Users\Models\Users
     */
    public function getIdentity()
    {
        $global_app_name = \Base::instance()->get('APP_NAME');
        
        $identity = $this->session->get('auth-identity');
        
        if (empty($identity->id)) {
        	return new \Users\Models\Users;
        }
        
        return $identity;
    }
    
    /**
     * Sets the current identity
     *
     * @return array
     */
    public function setIdentity( \Users\Models\Users $user )
    {
        $global_app_name = \Base::instance()->get('APP_NAME');
    
        return $this->session->set('auth-identity', $user);
    }    

    /**
     * Returns the current identity
     *
     * @return string
     */
    public function getName()
    {
        $global_app_name = \Base::instance()->get('APP_NAME');
        $identity = $this->getIdentity();
        
        $name = "";
        if (!empty($identity->first_name)) {
            $name .= $identity->first_name . " "; 
        }
        if (!empty($identity->last_name)) {
            $name .= $identity->last_name . " ";
        }        
        
        return trim($name);
    }

    /**
     * Removes the user identity information from session
     */
    private function remove()
    {
        /*
        if ($this->cookies->has('RMU')) {
            $this->cookies->get('RMU')->delete();
        }
        if ($this->cookies->has('RMT')) {
            $this->cookies->get('RMT')->delete();
        }
        */

        $this->session->remove('auth-identity');
    }

    /**
     * Auths the user by his/her id
     *
     * @param int $id
     */
    public function authUserById($id)
    {
        throw new \Exception('Not implemented yet');
        
        /*
        $user = \Users\Models\Users::findFirstById($id);
        if ($user == false) {
            throw new \Exception('The user does not exist');
        }

        $this->checkUserFlags($user);

        $this->session->set('auth-identity', array(
            'id' => $user->id,
            'name' => $user->name,
            'profile' => $user->profile->name
        ));
        */
    }
}
