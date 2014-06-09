<div class="row">
    <div class="col-md-2">
    
        <h3>Basics</h3>
                
    </div>
    <!-- /.col-md-2 -->
                
    <div class="col-md-10">

        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" value="<?php echo $flash->old('username'); ?>" class="form-control" />
        </div>
        <!-- /.form-group -->

        <div class="form-group">
            <label>First Name</label>
            <input type="text" name="first_name" value="<?php echo $flash->old('first_name'); ?>" class="form-control" />
        </div>
        <!-- /.form-group -->

        <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last_name" value="<?php echo $flash->old('last_name'); ?>" class="form-control" />
        </div>
        <!-- /.form-group -->

        <div class="form-group">
            <label>Email Address</label>
            <input type="text" name="email" value="<?php echo $flash->old('email'); ?>" class="form-control" />
        </div>
        <!-- /.form-group -->
        
        <div class="form-group">
            <label>New Password</label>
            <input type="password" name="new_password" class="form-control" autocomplete="off" />
        </div>
        <!-- /.form-group -->
        
        <div class="form-group">
            <label>Confirm New Password</label>
            <input type="password" name="confirm_new_password" class="form-control" autocomplete="off" />
        </div>
        <!-- /.form-group -->
        
    </div>
    <!-- /.col-md-10 -->
</div>
<!-- /.row -->

<hr />

<?php if( $canModifyRole ) { ?>
<div class="row">
    <div class="col-md-2">
    
        <h3>Role</h3>
                
    </div>
    <!-- /.col-md-2 -->
                
    <div class="col-md-10">                    
                    
        <div class="form-group">
            <label>Role</label>
			<select name="role" data-select='1' class="form-control">
                <?php echo \Dsc\Html\Select::options($roles, $flash->old('role')); ?>
			</select>
        </div>
        <!-- /.form-group -->
                    
    </div>
    <!-- /.col-md-10 -->
</div>
<!-- /.row -->

<hr />                    
<?php } ?>
