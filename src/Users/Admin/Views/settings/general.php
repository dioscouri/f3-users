<div class="row">
    <div class="col-md-12">
    
        <div class="row">
            <div class="col-md-2">

                <h3>Profiles</h3>

            </div>
            <!-- /.col-md-2 -->

            <div class="col-md-10">

                <div class="form-group">
                    <label>Enable Profile Pages?</label>
                    <select name="general[profiles][enabled]" class="form-control">
                        <option value="1" <?php if ($flash->old('general.profiles.enabled') == '1') { echo "selected='selected'"; } ?>>Yes</option>
                        <option value="0" <?php if ($flash->old('general.profiles.enabled') == '0') { echo "selected='selected'"; } ?>>No</option>                        
                    </select>                    
                </div>
                <!-- /.form-group -->

            </div>
            <!-- /.col-md-10 -->

        </div>
        <!-- /.row -->
        
        <hr/>    

        <div class="row">
            <div class="col-md-2">

                <h3>Registration</h3>

            </div>
            <!-- /.col-md-2 -->

            <div class="col-md-10">

                <div class="form-group">
                    <label>Enabled?</label>
                    <select name="general[registration][enabled]" class="form-control">
                        <option value="1" <?php if ($flash->old('general.registration.enabled') == '1') { echo "selected='selected'"; } ?>>Yes</option>
                        <option value="0" <?php if ($flash->old('general.registration.enabled') == '0') { echo "selected='selected'"; } ?>>No</option>                        
                    </select>                
                </div>
                <!-- /.form-group -->
                
                <div class="form-group">
                    <label>Include Username in registration form?</label>
                    <select name="general[registration][username]" class="form-control">
                        <option value="1" <?php if ($flash->old('general.registration.username') == '1') { echo "selected='selected'"; } ?>>Yes</option>
                        <option value="0" <?php if ($flash->old('general.registration.username') == '0') { echo "selected='selected'"; } ?>>No</option>                        
                    </select>                
                </div>
                <!-- /.form-group -->
                
                <div class="form-group">
                    <label>Enable the combined login/registration view?</label>
                    <select name="general[registration][dual]" class="form-control">
                        <option value="0" <?php if ($flash->old('general.registration.dual') == '0') { echo "selected='selected'"; } ?>>No</option>
                        <option value="1" <?php if ($flash->old('general.registration.dual') == '1') { echo "selected='selected'"; } ?>>Yes</option>                                                
                    </select>
                </div>
                <!-- /.form-group -->
                <div class="alert alert-info">
                    <p>If enabled, use this as your login form: <a target="_blank" href="./login">/login</a></p>
                    <p>If disabled, /login will automatically redirect to <a target="_blank" href="./sign-in">/sign-in</a> and you should use <a target="_blank" href="./register">/register</a> for registration.</p>
                    
                </div>

                <div class="form-group">
                    <label>After registration:</label>
                    <select name="general[registration][action]" class="form-control">
                        <option value="email_validation" <?php if ($flash->old('general.registration.action') == 'email_validation') { echo "selected='selected'"; } ?>>Require email validation before enabling login</option>
                        <option value="auto_login" <?php if ($flash->old('general.registration.action') == 'auto_login') { echo "selected='selected'"; } ?>>Automatically login the new user immediately</option>
                        <option value="auto_login_with_validation" <?php if ($flash->old('general.registration.action') == 'auto_login_with_validation') { echo "selected='selected'"; } ?>>Auto-login immediately, but require email validation for future logins</option>
                    </select>
                </div>
                <!-- /.form-group -->

            </div>
            <!-- /.col-md-10 -->

        </div>
        <!-- /.row -->
        
		<hr />
		
        <div class="row">
            <div class="col-md-2">

                <h3>Login</h3>

            </div>
            <!-- /.col-md-2 -->

            <div class="col-md-10">

                <div class="form-group">
                    <label>Auto-login token lifetime (in minutes)</label>
                    <input class="form-control" name="general[login][auto_login_token_lifetime]" value="<?php echo $flash->old('general.login.auto_login_token_lifetime'); ?>" placeholder="How long should auto login token last for (in minutes)" />
                </div>
                <!-- /.form-group -->

            </div>
            <!-- /.col-md-10 -->

        </div>
        <!-- /.row -->
        
    </div>
</div>
