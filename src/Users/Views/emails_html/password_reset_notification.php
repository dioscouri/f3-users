<?php $link = $SCHEME . '://' . $HOST . $BASE . '/user/forgot-password/'; ?>

<p><?php echo trim('Hi ' . $user->{'first_name'} ); ?>,</p>
<p>Your password was reset.  If these changes were made in error, please reset your account password immediately using the link below:</p>
<p><a href="<?php echo $link; ?>"><?php echo $link; ?></a></p>
<p>Thanks.</p> 