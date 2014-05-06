<div class="row">
    <div class="col-md-2">
    
        <h3>Details</h3>
                
    </div>
    <!-- /.col-md-2 -->
                
    <div class="col-md-10">

        <div class="form-group">
            <label>Title:</label>
            <input type="text" name="title" placeholder="Title" value="<?php echo $flash->old('title'); ?>" class="form-control" />
        </div>
        <!-- /.form-group -->
        
        <div class="form-group">
            <label>Slug:</label>
            <input type="text" name="slug" placeholder="Slug" value="<?php echo $flash->old('slug'); ?>" class="form-control" />
        </div>
        <!-- /.form-group -->
        
        <div class="form-group">
            <label>Description</label>
            <input type="text" name="description" placeholder="Short Description -- for admin-side display only" value="<?php echo $flash->old('description'); ?>" class="form-control" />
        </div>
        <!-- /.form-group -->
    
    </div>
    <!-- /.col-md-10 -->
</div>
<!-- /.row -->
