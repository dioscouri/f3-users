<?php 
	// find out how many social account the user can link to
	$settings = \Users\Models\Settings::fetch();
	$providers = $settings->enabledSocialProviders();
?>

<div class="container">
    <ol class="breadcrumb">
        <li>
            <a href="./user">My Account</a>
        </li>
        <li class="active">Social Profiles</li>
    </ol>
    
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <legend>
                Social Profiles
            </legend>
        </div>
    </div>
    <div class="row">
    	<h3>Connected Accounts</h3>
    	<?php if( empty( $user->social ) ) { ?>
        <div class="col-xs-12 col-sm-12 col-md-4">
    		<p>Currently, you do not have any social profiles linked to your account.</p>
        </div>
    	<?php } else  {
        	foreach( $user->social as $network => $profile ) { 
			// delete this network from list of networks to add link to
			if( ( $pos = in_array( $network, $providers ) ) !== false ) {
				unset( $providers[$pos] );
			}

			$profile_img = \Dsc\ArrayHelper::get($profile, 'profile.photoURL');
			$name = \Dsc\ArrayHelper::get($profile, 'profile.displayName');
			if( empty( $profile_img ) ) {
				$profile_img = './minify/Users/Assets/images/empty_profile.png';
			}
		?>
        <div class="col-xs-12 col-sm-12 col-md-4">
        	<h4><?php echo ucwords($network); ?></h4>
        	<div style="text-align : center;">
				<img src="<?php echo $profile_img; ?>" alt="<?php echo $name; ?>" class="img-rounded center-block" style="margin : 0 auto;" />
				<div>
					<a href="<?php echo \Dsc\ArrayHelper::get($profile, 'profile.profileURL'); ?>" target="_blank"><?php echo $name;?></a>
					- <a href="./user/social/unlink/<?php echo $network; ?>" class="text-danger">Unlink</a>
				</div>
        	</div>
        </div>        	
       <?php
        	}
        }?>
    </div>
    
    <?php if( !empty($providers ) ) { 
    	\Dsc\System::instance()->get( 'session' )->set( 'site.login.redirect', '/user/social-profiles' );
    	?>
    <div class="row">
    	<h3>Link your profile with</h3>
    	<div class="col-xs-12 col-sm-12 col-md-4">
    	<ul>
    		<?php foreach( $providers as $network ) { ?>
    		<li><a href="./login/social/auth/<?php echo $network;?>"><?php echo ucwords( $network ); ?> profile</a></li>
    		<?php } ?>
    	</ul>
        </div>
    </div>
    <?php } ?>
</div>