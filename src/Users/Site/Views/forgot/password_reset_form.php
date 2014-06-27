<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <legend>
                Reset Password
            </legend>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-4">
            <form action="./user/reset-password" method="post" class="form" role="form">
            
                <div class="form-group">
                    <label>New Password</label>
                    <input class="form-control" name="new_password" placeholder="New Password" type="password" required />
                </div>
                
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input class="form-control" name="confirm_new_password" placeholder="Confirm New Password" type="password" required />
                </div>
                            
                <button class="btn btn-lg btn-primary" type="submit">Submit</button>
                
            </form>
        </div>
    </div>
</div>