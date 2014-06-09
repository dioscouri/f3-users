<div class="row">
    <div class="col-md-2">
    
        <h3>Admin Tags</h3>
        <p class="help-block">Tags applied to the user for administrative use only</p>
                
    </div>
    <!-- /.col-md-2 -->
                
    <div class="col-md-10">

        <div class="form-group">
            <label>Separate tags by hitting Enter or with a comma.</label>
            <input name="admin_tags" data-tags='<?php echo json_encode( \Users\Models\Users::distinctAdminTags() ); ?>' value="<?php echo implode(",", (array) $flash->old('admin_tags') ); ?>" type="text" class="form-control ui-select2-tags" /> 
        </div>
        <!-- /.form-group -->
    
    </div>
    <!-- /.col-md-10 -->
</div>
<!-- /.row -->