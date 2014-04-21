<div class="container">

<h2>
    Complete Profile <small>Final Step</small>
</h2>

<form action="./login/submitCompleteProfile" method="post">

    <div class="well well-sm">
        <?php if (empty($model->email)) { ?>
        <div class="form-group">
            <input type="text" class="form-control" data-required="true" required="required" name="email" value="" placeholder="Email" autocomplete="email">
        </div>
        <?php } ?>
    </div>

    <div class="input-group form-group">
        <button type="submit" class="btn btn-default custom-button btn-lg">Create Profile</button>
    </div>    
    
</form>

</div>