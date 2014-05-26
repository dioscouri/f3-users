<div class="container">
    <?php 
    $settings = \Users\Models\Settings::fetch();
    if ($settings->isSocialLoginEnabled()) 
    {
        ?>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3">
            <legend>
                Sign in with a social profile
            </legend>
                        
            <?php echo $this->renderLayout('Users/Site/Views::login/social.php'); ?>
            
            <p>&nbsp;</p>
            
            </div>
        </div>
        <?php  
    } 
    ?>
    
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3">
            <legend>
                Sign in with your email address
            </legend>
            
            <form action="./login" method="post" class="form" role="form">
                <div class="form-group">
                    <label>Email Address</label>
                    <input class="form-control" name="login-username" placeholder="Email Address" type="email" required />
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <input class="form-control" name="login-password" placeholder="Password" type="password" required />
                </div>
                
                <div class="form-group">            
                    <button class="btn btn-lg btn-primary" type="submit">Sign In</button>
                    <a class="btn btn-link" href="./user/forgot-password">Forgot your password?</a>
                </div>
                
                <?php if ($settings->{'general.registration.enabled'}) { ?>
                <div class="form-group">
                    <a href="./register">Register with us</a>
                </div>
                <?php } ?>
                
            </form>
        </div>
    </div>
    
</div>