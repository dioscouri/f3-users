<?php $identity = $this->auth->getIdentity(); ?>
<?php if($url = $identity->profilePicture()) : ?>
<img src="<?php echo $url; ?>">
<?php endif;?>

<form method="post" enctype="multipart/form-data">
<input name="avatar" type="file">
<button type="submit">Submit</button>
</form>