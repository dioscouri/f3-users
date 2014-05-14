<div class="row">
    <div class="col-md-12">

        <div class="row">
            <div class="col-md-2">

                <h3>Registration</h3>

            </div>
            <!-- /.col-md-2 -->

            <div class="col-md-10">

                <?php /* ?>
                <div class="form-group">
                    <label>Enabled?</label>
                    <select name="general[registration][enabled]" class="form-control">
                        <option value="1" <?php if ($flash->old('general.registration.enabled') == '1') { echo "selected='selected'"; } ?>>Yes</option>
                        <option value="0" <?php if ($flash->old('general.registration.enabled') == '0') { echo "selected='selected'"; } ?>>No</option>                        
                    </select>                
                </div>
                <!-- /.form-group -->
                */ ?>
                
                <div class="form-group">
                    <label>Include Username in registration form?</label>
                    <select name="general[registration][username]" class="form-control">
                        <option value="1" <?php if ($flash->old('general.registration.username') == '1') { echo "selected='selected'"; } ?>>Yes</option>
                        <option value="0" <?php if ($flash->old('general.registration.username') == '0') { echo "selected='selected'"; } ?>>No</option>                        
                    </select>                
                </div>
                <!-- /.form-group -->

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

    </div>
</div>
