<?php $link = $SCHEME . '://' . $HOST . $BASE . '/user/change-email/confirm?new_email=' . urlencode( $user->{'change_email.email'} ) . '&token=' . $user->{'change_email.token'}; ?>

<p><?php echo trim('Hello ' . $user->{'first_name'} ); ?>!</p>
<p>Please click the link below to verify and change your email address:</p>
<p><a href="<?php echo $link; ?>"><?php echo $link; ?></a></p>
<p>If you have problems, please copy and paste the above URL into your web browser.</p>
<p>Your token is: <?php echo $user->{'change_email.token'}; ?></p>
<p>Thanks.</p> 