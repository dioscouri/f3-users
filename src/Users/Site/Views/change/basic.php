<div class="container">
    <ol class="breadcrumb">
        <li>
            <a href="./user">My Profile</a>
        </li>
        <li>
            <a href="./user/settings">Settings</a>
        </li>
        <li class="active">Change Basic Information</li>
    </ol>
    
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-4">
            <legend>
                Change Basic Information
            </legend>
                        
            <form action="./user/change-basic" method="post" class="form" role="form">
                <div class="form-group">
                    <label>Your Username</label>
                    <input class="form-control" name="username" placeholder="<?php echo $identity->{'username'}; ?>" value="<?php echo $identity->{'username'}; ?>" type="text" required />
                </div>
                
            	<div class="form-group">
                    <label>Your First Name</label>
                    <input class="form-control" name="first_name" placeholder="<?php echo $identity->{'first_name'}; ?>" value="<?php echo $identity->{'first_name'}; ?>" type="text" required />
                </div>
                
                <div class="form-group">
                    <label>Your Last Name</label>
                    <input class="form-control" name="last_name" placeholder="<?php echo $identity->{'last_name'}; ?>" value="<?php echo $identity->{'last_name'}; ?>" type="text" required />
                </div>
                
                <div class="form-group">
                    <label>Birthday</label>
                    <div class="input-group">
                        <input class="ui-datepicker form-control" name="birthday" value="<?php echo $identity->{'birthday'}; ?>" type="text" data-date-format="yyyy-mm-dd" data-start-view="2">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>                    
                    <p class="help-block">Please use the format: YYYY-MM-DD</p>
                </div>
                
                <button class="btn btn-lg btn-primary" type="submit">Submit</button>
                <a class="btn btn-link" href="./user/settings">Cancel</a>
                
            </form>
        </div>
    </div>
</div>

<!-- datepicker -->
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" type="text/css" />

<script>
jQuery(document).ready(function(){
    if (jQuery.fn.datepicker) { jQuery('.ui-datepicker').datepicker ({ autoclose: true, startView: 2 }); }
});
</script>