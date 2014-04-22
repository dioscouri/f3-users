<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-4">
            <legend>
                Sign in with your email address
            </legend>
            
            <form action="./login" method="post" class="form" role="form">
                <div class="form-group">
                <input class="form-control" name="login-username" placeholder="Your Email" type="email" /> 
                <input class="form-control" name="login-password" placeholder="New Password" type="password" /> 
                <br />
                <button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
                </div>
            </form>
        </div>
        
        <?php 
        if (class_exists('Hybrid_Auth')) 
        {
            ?>
            <div class="col-xs-12 col-sm-12 col-md-4 col-md-offset-1">
            <?php // TODO Only display this if there is a properly configured, enabled Provider ?>
            <legend>
                or with a social profile
            </legend>
                        
            <?php echo $this->renderLayout('Users/Site/Views::login/hybrid.php'); ?>
            </div>
            <?php  
        } 
        ?>
    </div>
</div>