<?php $settings = \Users\Models\Settings::fetch(); ?>

<?php if ($settings->{'social.providers.Facebook.enabled'} && $settings->{'social.providers.Facebook.keys.id'} && $settings->{'social.providers.Facebook.keys.secret'}) { ?>
<div class="form-group">
    <a href="./login/social/auth/facebook" class="btn btn-facebook btn-default">
    <i class="fa fa-facebook"></i> <span>Facebook</span>
    </a>
</div>
<?php } ?>
    
<?php if ($settings->{'social.providers.Twitter.enabled'} && $settings->{'social.providers.Twitter.keys.key'} && $settings->{'social.providers.Twitter.keys.secret'}) { ?>
<div class="form-group">
    <a href="./login/social/auth/twitter" class="btn btn-twitter btn-default">
    <i class="fa fa-twitter"></i> <span>Twitter</span>
    </a>
</div>
<?php } ?>

<?php if ($settings->{'social.providers.Google.enabled'} && $settings->{'social.providers.Google.keys.id'} && $settings->{'social.providers.Google.keys.secret'}) { ?>
<div class="form-group">
    <a href="./login/social/auth/google" class="btn btn-google btn-default">
    <i class="fa fa-google"></i> <span>Google</span>
    </a>
</div>
<?php } ?>

<?php if ($settings->{'social.providers.Linkedin.enabled'} && $settings->{'social.providers.Linkedin.keys.id'} && $settings->{'social.providers.Linkedin.keys.secret'}) { ?>
<div class="form-group">
    <a href="./login/social/auth/linkedin" class="btn btn-linkedin btn-default">
    <i class="fa fa-linkedin"></i> <span>LinkedIn</span>
    </a>
</div>
<?php } ?>
