<?php
namespace Users\Models;

class Settings extends \Dsc\Mongo\Collections\Settings
{

    public $general = array(
        'registration' => array(
            'enabled' => 1,
            'username' => 1,
            'action' => 'email_validation'
        )
    );

    public $social = array();

    protected $__type = 'users.settings';

    public function isSocialLoginEnabled($provider=null)
    {
        if (!class_exists('Hybrid_Auth'))
        {
            // no social profiles are allowed unless there is Hybrid Auth
            return false;
        }
        
        $result = false;
        switch ($provider)
        {
            case 'facebook':
                $result = $this->{'social.providers.Facebook.enabled'} && $this->{'social.providers.Facebook.keys.id'} && $this->{'social.providers.Facebook.keys.secret'};
                break;
            case 'twitter':
                $result = $this->{'social.providers.Twitter.enabled'} && $this->{'social.providers.Twitter.keys.key'} && $this->{'social.providers.Twitter.keys.secret'};
                break;
            case 'linkedin':
                $result = $this->{'social.providers.Linkedin.enabled'} && $this->{'social.providers.Linkedin.keys.id'} && $this->{'social.providers.Linkedin.keys.secret'};
                break;
            case 'google':
                $result = $this->{'social.providers.Google.enabled'} && $this->{'social.providers.Google.keys.id'} && $this->{'social.providers.Google.keys.secret'};
                break;
            default:
                // are ANY of the social providers enabled?
                $enabled = $this->enabledSocialProviders();
                if (!empty($enabled)) {
                	$result = true;
                }
                break;
        }
        
        return $result;
    }

    public function enabledSocialProviders()
    {
        $providers = array();
        foreach ((array) $this->{'social.providers'} as $network => $opts)
        {
            if ($this->isSocialLoginEnabled(strtolower($network)))
            {
                $providers[] = strtolower($network);
            }
        }
        return $providers;
    }
}