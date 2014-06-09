<div class="row">
    <div class="col-md-2">
    
        <h3>Tags</h3>
        <p class="help-block">Tags applied by the user</p>
                
    </div>
    <!-- /.col-md-2 -->
                
    <div class="col-md-10">

        <div class="form-group">
            <label>Separate tags by hitting Enter or with a comma.</label>
            <input name="tags" data-tags='<?php echo json_encode( \Users\Models\Users::distinctTags() ); ?>' value="<?php echo implode(",", (array) $flash->old('tags') ); ?>" type="text" class="form-control ui-select2-tags" /> 
        </div>
        <!-- /.form-group -->
    
    </div>
    <!-- /.col-md-10 -->
</div>
<!-- /.row -->