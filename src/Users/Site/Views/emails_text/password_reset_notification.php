<?php $link = $SCHEME . '://' . $HOST . $BASE . '/user/forgot-password/'; ?>
<?php echo trim('Hi ' . $user->{'first_name'} ); ?>,

Your password was reset.  If these changes were made in error, please reset your account password immediately using the link below:

<?php echo $link; ?>

Thanks.
