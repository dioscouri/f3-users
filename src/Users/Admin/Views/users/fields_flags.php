<div class="row">
    <div class="col-md-2">
        
        <h3>Status Flags</h3>
                
    </div>
    <!-- /.col-md-2 -->
                
    <div class="col-md-10">
        
        <div class="form-group">
            <div class="row clearfix">
                <div class="col-md-4">
                    <div>Active</div>
                    <label class="radio-inline">
                        <input type="radio" name="active" value="1" <?php if ($flash->old('active')) { echo 'checked'; } ?>> Yes
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="active" value="0" <?php if (!$flash->old('active')) { echo 'checked'; } ?>> No
                    </label>
                </div>
                
                <div class="col-md-4">
                    <div>Banned</div>
                    <label class="radio-inline">
                        <input type="radio" name="banned" value="1" <?php if ($flash->old('banned')) { echo 'checked'; } ?>> Yes
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="banned" value="0" <?php if (!$flash->old('banned')) { echo 'checked'; } ?>> No
                    </label>
                </div>
                
                <div class="col-md-4">
                    <div>Suspended</div>
                    <label class="radio-inline">
                        <input type="radio" name="suspended" value="1" <?php if ($flash->old('suspended')) { echo 'checked'; } ?>> Yes
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="suspended" value="0" <?php if (!$flash->old('suspended')) { echo 'checked'; } ?>> No
                    </label>
                </div>
                
            </div>
        </div>
        <!-- /.form-group -->
    
    </div>
    <!-- /.col-md-10 -->
    
</div>
<!-- /.row -->

<hr />