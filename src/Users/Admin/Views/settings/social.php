<h3 class="">Social Settings</h3>


<div class="form-group">
    

    <div class="col-md-7">
        <label>Base URL</label>
        <input name="social[base_url]" type="url" required="required" value="<?php echo $flash->old('social.base_url'); ?>">
        <?php if(empty($flash->old('social.base_url'))) : ?>
        <pre>http://your-site-url.com/social</pre>
        <?php endif; ?>
        <h2>Providers</h2>
        <fieldset>
        <legend>Facebook</legend>
        <label>Facebook enabled</label>
        <label class="radio-inline">
            <input type="radio" name="social[providers][Facebook][enabled]" value="false" <?php if ($flash->old('social.providers.Facebook.enabled')) { echo "checked"; } ?>> False
        </label>
        <label class="radio-inline">
            <input type="radio" name="social[providers][Facebook][enabled]" value="true" <?php if ($flash->old('social.providers.Facebook.enabled')) { echo "checked"; } ?>> True
        </label>
        <div>
        <label>Facebook App ID</label>
        <input type="text" name="social[providers][Facebook][keys][id]" value="<?php echo $flash->old('social.providers.Facebook.keys.id'); ?>" >
        </div>
        <div>
        <label>Facebook App secret</label>
        <input type="text" name="social[providers][Facebook][keys][secret]" value="<?php echo $flash->old('social.providers.Facebook.keys.secret'); ?>" >
        </div>
        </fieldset>
        <fieldset>
        <legend>Twitter</legend>
        <label>Twitter enabled</label>
         <label class="radio-inline">
            <input type="radio" name="social[providers][Twitter][enabled]" value="false" <?php if ($flash->old('social.providers.Twitter.enabled')) { echo "checked"; } ?>> False
        </label>
        <label class="radio-inline">
            <input type="radio" name="social[providers][Twitter][enabled]" value="true" <?php if ($flash->old('social.providers.Twitter.enabled')) { echo "checked"; } ?>> True
        </label> 
        <div>
        <label>Twitter App ID</label>
        <input type="text" name="social[providers][Twitter][keys][id]" value="<?php echo $flash->old('social.providers.Twitter.keys.id'); ?>" >
        </div>
        <div>
        <label>Twitter App secret</label>
        <input type="text" name="social[providers][Twitter][keys][secret]" value="<?php echo $flash->old('social.providers.Twitter.keys.secret'); ?>" >
        </div>
        </fieldset>
        
    </div>
</div>
