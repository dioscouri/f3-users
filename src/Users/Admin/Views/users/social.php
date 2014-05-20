<?php 
foreach( (array)$flash->old('social') as $network=> $profile ){
$profile_img = \Dsc\ArrayHelper::get($profile, 'profile.photoURL');
$name = \Dsc\ArrayHelper::get($profile, 'profile.displayName');

if( empty( $profile_img ) ) {
	$profile_img = './minify/Users/Assets/images/empty_profile.png';
}	
?>
<div class="panel panel-default">
	<div class="panel-body">
		<div class="row">
			<div class="col-xs-12 col-ms-12 col-md-3">
				<legend><?php echo ucwords( $network ); ?></legend>
                    <img src="<?php echo $profile_img; ?>" alt="<?php echo $name; ?>" class="img-responsive" />
			</div>
			<div class="col-xs-12 col-ms-12 col-md-9">
				<a href="<?php echo $profile['profile']['profileURL']; ?>" target="_blank" title="<?php echo $name.' on '.ucwords( $network ); ?>">
					<legend><?php echo $name; ?></legend>
				</a>
				<div class="row">
					<div class="col-xs-12 col-ms-6 col-md-6 col-lg-4">
						<h4>Basic <small>Information</small></h4>
						
						<div class="col-xs-5 col-ms-4 col-md-4 col-lg-3">
							Profile:
						</div>
						<div class="col-xs-7 col-ms-8 col-md-8 col-lg-9">
							<a href="<?php echo $profile['profile']['profileURL']; ?>"><?php echo $name; ?></a>
						</div>
						
						<div class="col-xs-5 col-ms-4 col-md-4 col-lg-3">
							Email:
						</div>
						<div class="col-xs-7 col-ms-8 col-md-8 col-lg-9">
							<?php 
								if( empty($profile['profile']['email']) ) {
									echo '-';
								} else {
								?>
							<a href="mailto:<?php echo $profile['profile']['email']; ?>"><?php echo $profile['profile']['email']; ?></a>
							<?php } ?>
						</div>
						
						<div class="col-xs-5 col-ms-4 col-md-4 col-lg-3">
							Age:
						</div>
						<div class="col-xs-7 col-ms-8 col-md-8 col-lg-9">
							<?php 
								if( empty($profile['profile']['age']) ) {
									echo '-';
								} else {
									echo $profile['profile']['age'];
								}
							?>
						</div>
						
					</div>
				
					<div class="col-xs-12 col-ms-6 col-md-6 col-lg-4">
						<h4>Additional <small>Information</small></h4>
						<div class="col-xs-5 col-ms-4 col-md-4 col-lg-3">
							Gender:
						</div>
						<div class="col-xs-7 col-ms-8 col-md-8 col-lg-9">
							<?php
								if( empty($profile['profile']['gender'] ) ) {
									echo '-';
								} else {
									echo $profile['profile']['gender'];
								}
							?>
						</div>
						
						<div class="col-xs-5 col-ms-4 col-md-4 col-lg-3">
							Birthday:
						</div>
						<div class="col-xs-7 col-ms-8 col-md-8 col-lg-9">
							<?php 
								if( empty($profile['profile']['birthDay']) ) {
									echo '-';
								} else {
									echo $profile['profile']['birthMonth'].'/'.$profile['profile']['birthDay'].'/'.$profile['profile']['birthYear'];
								}
							?>
						</div>
						
						<?php if( !empty( $profile['profile']['address']) ) {?>
						<p>&nbsp;</p>
						
						<div class="well col-xs-12 col-ms-12 col-md-12 col-lg-12">
							<legend>Address</legend>
							<address>
							  <strong><?php echo $name; ?></strong><br>
							  <?php echo $profile['profile']['address']; ?><br>
							  <?php echo $profile['profile']['city'].', '.$profile['profile']['region'].' '.$profile['profile']['zip']; ?>
							  <?php if(!empty( $profile['profile']['phone'] ) ) {?>
							  <br><abbr title="Phone">P:</abbr> (123) 456-7890
							  <?php } ?>
							</address>
						</div>
						<?php } ?>
					</div>
					
					<div class="col-xs-12 col-ms-6 col-md-6 col-lg-4">
						<h4>Login <small>Information</small></h4>
						<div class="col-xs-5 col-ms-4 col-md-4 col-lg-3">
							Expires:
						</div>
						<div class="col-xs-7 col-ms-8 col-md-8 col-lg-9">
							<?php 
								if (empty( $profile['access_token']['expires_at']) ) {
									echo '-';
								} else {
									echo date( 'm/d/Y h:iA' , $profile['access_token']['expires_at']);
								}?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
}