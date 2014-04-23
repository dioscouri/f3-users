<?php $link = $SCHEME . '://' . $HOST . $BASE . '/user/reset-password/' . $user->{'forgot_password.token'}; ?>
<?php echo trim('Hi ' . $user->{'first_name'} ); ?>,

You recently requested a link to reset your password.  Please set a new password by following the link below:

<?php echo $link; ?>

If you did not make this request, feel free to ignore this email.

Thanks.
