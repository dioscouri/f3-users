<div id="user-profile" class="container">
    <h2>
        <?php echo $user->fullName(); ?>
    </h2>
    <div id="profile-container">
        <div class="row">
            <div class="col-md-9 col-sm-12">
                <div class="row">
                    <?php if ($profilePictureSrc = $user->profilePicture()) { ?>
                        <div class="col-md-3 col-sm-12">
                            <p><img src="<?php echo $profilePictureSrc; ?>" alt="Profile Picture" class="img-responsive" /></p>
                        </div>
                    <?php } ?>
                    <div class="col-md-9 col-sm-12">
                        <ul class="icons-list">
                            <li>
                                <i class="icon-li fa fa-calendar"></i> <b>Last Visit:</b> <?php echo date('Y-m-d', $user->{'last_visit.time'}); ?>
                            </li>
                            <li>
                                <i class="icon-li fa fa-clock-o"></i> <b>Joined:</b> <?php echo date('Y-m-d', $user->{'metadata.created.time'}); ?>
                            </li>                            
                        </ul>
                    </div>
                </div>

                <?php /* ?>
                <?php echo \Dsc\Debug::dump( $user ); ?>
                <h4>Public Activity</h4>
                <?php */ ?>
            </div>
            <div class="col-md-3 col-sm-6">
                <?php /* ?>
                <?php echo \Dsc\Debug::dump( $user ); ?>
                <h4>Easy Statistics</h4>
                <ul class="icons-list">
                    <li>
                        <i class="icon-li fa fa-envelope"></i> rod@jumpstartui.com
                    </li>
                    <li>
                        <i class="icon-li fa fa-globe"></i> jumstartthemes.com
                    </li>
                    <li>
                        <i class="icon-li fa fa-map-marker"></i> Las Vegas, NV
                    </li>
                </ul>                
                <div class="list-group">
                    <a href="javascript:;" class="list-group-item">
                        <h3 class="pull-right">
                            <i class="fa fa-eye"></i>
                        </h3>
                        <h4 class="list-group-item-heading">38,847</h4>
                        <p class="list-group-item-text">Profile Views</p>
                    </a>
                    <a href="javascript:;" class="list-group-item">
                        <h3 class="pull-right">
                            <i class="fa fa-facebook-square"></i>
                        </h3>
                        <h4 class="list-group-item-heading">3,482</h4>
                        <p class="list-group-item-text">Facebook Likes</p>
                    </a>
                    <a href="javascript:;" class="list-group-item">
                        <h3 class="pull-right">
                            <i class="fa fa-twitter-square"></i>
                        </h3>
                        <h4 class="list-group-item-heading">5,845</h4>
                        <p class="list-group-item-text">Twitter Followers</p>
                    </a>
                </div>
                <!-- /.list-group -->
                <br />
                <div class="well">
                    <h4>Recent Activity</h4>
                    <ul class="icons-list text-md">
                        <li>
                            <i class="icon-li fa fa-location-arrow"></i> <strong>Rod</strong> uploaded 6 files. <br /> <small>about 4 hours ago</small>
                        </li>
                        <li>
                            <i class="icon-li fa fa-location-arrow"></i> <strong>Rod</strong> followed the research interest:
                            <a href="javascript:;">Open Access Books in Linguistics</a>
                            . <br /> <small>about 23 hours ago</small>
                        </li>
                        <li>
                            <i class="icon-li fa fa-location-arrow"></i> <strong>Rod</strong> added 51 papers. <br /> <small>2 days ago</small>
                        </li>
                    </ul>
                </div>
                */ ?>
            </div>
        </div>
        <!-- /.row -->
    </div>
    <!-- /#profile-container -->
</div>
<!-- #user-profile -->
