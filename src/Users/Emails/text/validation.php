<?php $link = $SCHEME . '://' . $HOST . $BASE . '/login/validate/token/' . $user->id; ?>
<?php echo trim('Hello ' . $user->{'first_name'} ); ?>!

Thanks for creating an account with us.  Please copy and paste the above URL into your web browser:

<?php echo $link; ?>

Your token is: <?php echo $user->id; ?>

Thanks.
