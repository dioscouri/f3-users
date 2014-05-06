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
        
        <div class="form-group">
            <?php if ($parents = \Users\Models\Roles::find()) { ?>
            <label>Parent</label>
            <div> 
                <select name="parent" class="form-control">
                    <option value="null">None</option>
                    <?php foreach ($parents as $parent) { ?>
                        <?php
                        if (strpos($parent->path, $flash->old('path')) !== false) {
                            // an item cannot be its own descendant
                            continue;
                        }
                        ?>
                    
                        <option value="<?php echo $parent->id; ?>" <?php if ($parent->id == $flash->old('parent')) { echo "selected='selected'"; } ?>><?php echo @str_repeat( "&ndash;", substr_count( @$parent->path, "/" ) - 1 ) . " " . $parent->title; ?></option>                    
                    <?php } ?> 
                </select>
            </div>
            <?php } ?>
        </div>
        <!-- /.form-group -->
    
    </div>
    <!-- /.col-md-10 -->
</div>
<!-- /.row -->
