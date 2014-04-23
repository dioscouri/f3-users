<?php $link = $SCHEME . '://' . $HOST . $BASE . '/user/reset-password/' . $user->{'forgot_password.token'}; ?>

<p><?php echo trim('Hi ' . $user->{'first_name'} ); ?>,</p>
<p>You recently requested a link to reset your password.  Please set a new password by following the link below:</p>
<p><a href="<?php echo $link; ?>"><?php echo $link; ?></a></p>
<p>If you did not make this request, feel free to ignore this email.</p> 
<p>Thanks.</p> 