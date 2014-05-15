<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-4">
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
                    <button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
                </div>
                
                <p><a href="./user/forgot-password">Forgot your password?</a></p>
                
            </form>
        </div>
        
        <?php 
        if (class_exists('Hybrid_Auth')) 
        {
            ?>
            <div class="col-xs-12 col-sm-12 col-md-4 col-md-offset-1">
            <legend>
                or with a social profile
            </legend>
                        
            <?php echo $this->renderLayout('Users/Site/Views::login/social.php'); ?>
            </div>
            <?php  
        } 
        ?>
    </div>
</div>