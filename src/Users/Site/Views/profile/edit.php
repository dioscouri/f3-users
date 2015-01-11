<div id="my-profile" class="container">
    <h2>
        <small>Hello <?php echo $this->auth->getIdentity()->fullName(); ?><br/></small>
        Your Account
        <a class="pull-right btn btn-default btn-small" href="./user"><i class="fa fa-angle-left"></i> Back to Profile</a>
    </h2>
    
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-4">
                    <legend>Settings</legend>
                    <p class="help-block"><small>Change your password, email, and basic information.</small></p>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-8">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-6">
                            <h4>Account Settings</h4>
                            <ul class="list-unstyled">
                                <li><a href="./user/change-basic">Change basic information</a></li>
                            	<li><a href="./user/change-email">Change email</a></li>
                                <li><a href="./user/change-password">Change password</a></li>
                            </ul>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-6">
                            <?php
                            $settings = \Users\Models\Settings::fetch();
                            if ($settings->isSocialLoginEnabled()) { ?>
                                <h4>Social</h4>
                                <ul class="list-unstyled">
                                    <li><a href="./user/social-profiles">Linked Social Profiles</a></li>
                                </ul>
                            <?php } ?>
                            
                            <?php /* ?>
                            <h4>Newsletters</h4>
                            <ul class="list-unstyled">
                                <li>Manage subscriptions</li>
                            </ul>
                            */ ?>
                            
                        </div>
                    </div>                
                </div>
            </div>
        </div>
    </div>
    
    <?php if (class_exists('\Shop\Models\Settings')) { ?>
    <?php $settings = \Shop\Models\Settings::fetch(); ?>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-4">
                    <legend>Shopping Personalization</legend>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-8">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-6">
                            <h4>Address Book</h4>
                            <ul class="list-unstyled">
                                <li><a href="./shop/account/addresses">Manage existing addresses</a></li>
                                <li><a href="./shop/account/addresses/create">Add new address</a></li>
                            </ul>
                            <?php /* ?>
                            <h4>Payment Methods</h4>
                            <ul class="list-unstyled">
                                <li><a href="./user/credit-cards">Manage credit cards</a></li>
                            </ul>    
                            */ ?>                        
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-6">
                            <h4>Lists</h4>
                            <ul class="list-unstyled">
                                <li><a href="./shop/wishlist">Wishlist</a></li>
                            </ul>
                            
                            <?php if ($settings->{'reviews.enabled'}) { ?>
                                <h4>Product Reviews</h4>
                                <ul class="list-unstyled">
                                    <li><a href="./shop/account/product-reviews">Your Reviews</a></li>
                                </ul>                            
                            <?php } ?>
                    
                        </div>
                    </div>                
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
    
    <?php if (class_exists('\Affiliates\Models\Referrals')) { ?>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-4">
                    <legend>Affiliate Account</legend>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-8">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-6">
                        
                            <h4>Referrals</h4>
                            <ul class="list-unstyled">
                                <li><a href="./affiliate/dashboard">Your affiliate account</a></li>
                                <li><a href="./affiliate/invite-friends">Invite friends</a></li>
                                <li><a href="./affiliate/invite-history">Your invite history</a></li>
                            </ul>
                        
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-6">
                            
                        </div>
                    </div>                
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
        
</div>