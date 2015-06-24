<?php $link = $SCHEME . '://' . $HOST . $BASE . '/user/change-email/confirm?new_email=' . urlencode( $user->{'change_email.email'} ) . '&token=' . $user->{'change_email.token'}; ?>
<?php echo trim('Hello ' . $user->{'first_name'} ); ?>!  
 
Please copy and paste the above URL into your web browser to change your email address: 
 
<?php echo $link; ?> 
 
Your token is: <?php echo $user->{'change_email.token'}; ?> 
 
Thanks. 
