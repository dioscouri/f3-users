<div class="container">
    <ol class="breadcrumb">
        <li>
            <a href="./user">My Account</a>
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
                
                <button class="btn btn-lg btn-primary" type="submit">Submit</button>
                <a class="btn btn-link" href="./user">Cancel</a>
                
            </form>
        </div>
    </div>
</div>