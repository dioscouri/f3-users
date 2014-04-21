<?php $settings = \Users\Models\Settings::fetch(); ?>

<?php if ($settings->{'social.providers.Facebook.enabled'} && $settings->{'social.providers.Facebook.keys.id'} && $settings->{'social.providers.Facebook.keys.secret'}) { ?>
<div class="form-group">
    <a href="./login/social/auth/facebook" class="btn btn-facebook btn-default">
    <i class="fa fa-facebook"></i> &nbsp;&nbsp;Login with Facebook
    </a>
</div>
<?php } ?>
    
<?php if ($settings->{'social.providers.Twitter.enabled'} && $settings->{'social.providers.Twitter.keys.id'} && $settings->{'social.providers.Twitter.keys.secret'}) { ?>
<div class="form-group">
    <a href="./login/social/auth/twitter" class="btn btn-twitter btn-default">
    <i class="fa fa-twitter"></i> &nbsp;&nbsp;Login with Twitter
    </a>
</div>
<?php } ?>

<?php if ($settings->{'social.providers.Google.enabled'}) { ?>
<div class="form-group">
    <a href="./login/social/auth/google" class="btn btn-google btn-default">
    <i class="fa fa-google"></i> &nbsp;&nbsp;Login with Google
    </a>
</div>
<?php } ?>

<?php if ($settings->{'social.providers.Linkedin.enabled'}) { ?>
<div class="form-group">
    <a href="./login/social/auth/linkedin" class="btn btn-linkedin btn-default">
    <i class="fa fa-linkedin"></i> &nbsp;&nbsp;Login with LinkedIn
    </a>
</div>
<?php } ?>
