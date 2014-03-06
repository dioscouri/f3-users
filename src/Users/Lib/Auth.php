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
        $username_input = $this->inputfilter->clean( $credentials['login-username'], 'alnum' );
        $password_input = $this->inputfilter->clean( $credentials['login-password'] );
        
        // check if safemode is being used
        $safemode_enabled = \Base::instance()->get('safemode.enabled');
        $safemode_user = \Base::instance()->get('safemode.username');
        $safemode_password = \Base::instance()->get('safemode.password');
        
        if ($safemode_enabled && $username_input === $safemode_user)
        {
            if (password_verify($password_input, $safemode_password))
            {
                $user = new \Users\Models\Users;
                $user->id = new \MongoId;
                $user->username = $safemode_user;
                $user->first_name = $safemode_user;
                $user->password = $safemode_password;
                $user->email = \Base::instance()->get('safemode.email');
                $role = \Base::instance()->get('safemode.role');
                if (!$role) {
                    $role = 'root';
                }
                $user->role = $role;
        
                $this->setIdentity( $user );
                return true;
            }
        }

        // now check standard login via username
        $model = new \Users\Models\Users;
        $model->setState('filter.username', $username_input);
        
        try {
            $item = $model->getItem();
        } catch ( \Exception $e ) {
            throw new \Exception('Invalid Username');
        }
        
        if (password_verify($password_input, $item->password))
        {
            $this->setIdentity( $item );
            return true;
        }        
        
        throw new \Exception('Invalid login');
        
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

        // TODO move this somewhere else
        $this->session->set('auth-identity', array(
            'id' => $user->id,
            'name' => $user->name,
            'profile' => $user->profile->name
        ));
        */
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
        /*
        if ($user->active != 'Y') {
            throw new \Exception('The user is inactive');
        }

        if ($user->banned != 'N') {
            throw new \Exception('The user is banned');
        }

        if ($user->suspended != 'N') {
            throw new \Exception('The user is suspended');
        }
        */
    }

    /**
     * Returns the current identity
     *
     * @return array
     */
    public function getIdentity()
    {
        $global_app_name = \Base::instance()->get('APP_NAME');
        
        return $this->session->get('auth-identity.'.$global_app_name);
    }
    
    /**
     * Returns the current identity
     *
     * @return array
     */
    public function setIdentity( \Users\Models\Users $user )
    {
        $global_app_name = \Base::instance()->get('APP_NAME');
    
        return $this->session->set('auth-identity.'.$global_app_name, $user);
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
    public function remove()
    {
        /*
        if ($this->cookies->has('RMU')) {
            $this->cookies->get('RMU')->delete();
        }
        if ($this->cookies->has('RMT')) {
            $this->cookies->get('RMT')->delete();
        }
        */
        
        $global_app_name = \Base::instance()->get('APP_NAME');
        $this->session->remove('auth-identity.'.$global_app_name);
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