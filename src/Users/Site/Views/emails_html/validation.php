<?php $link = $SCHEME . '://' . $HOST . $BASE . '/login/validate/token/' . $user->id; ?>

<p><?php echo trim('Hello ' . $user->{'first_name'} ); ?>!</p>
<p>Thanks for creating an account with us.  Please click the link below to confirm your email address:</p>
<p><a href="<?php echo $link; ?>"><?php echo $link; ?></a></p>
<p>If you have problems, please copy and paste the above URL into your web browser.</p>
<p>Your token is: <?php echo $user->id; ?></p>
<p>Thanks.</p> 