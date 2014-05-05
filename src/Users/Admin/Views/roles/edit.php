<div class="well">

<form id="detail-form" class="form-horizontal" method="post">

    <div class="row">
        <div class="col-md-12">
        
            <div class="clearfix">

                <div class="pull-right">
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <input id="primarySubmit" type="hidden" value="save_edit" name="submitType" />
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li>
                                <a onclick="document.getElementById('primarySubmit').value='save_close'; document.getElementById('detail-form').submit();" href="javascript:void(0);">Save & Close</a>
                            </li>
                        </ul>
                    </div>

                    &nbsp;
                    <a class="btn btn-default" href="/admin/users/roles">Cancel</a>
                </div>

            </div>
            <!-- /.form-actions -->
            
            <hr />
        
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#tab-basics" data-toggle="tab"> Basics </a>
                </li>
                <li>
                    <a href="#tab-permissions" data-toggle="tab"> Permissions </a>
                </li>
                <?php foreach ((array) $this->event->getArgument('tabs') as $key => $title ) { ?>
                <li>
                    <a href="#tab-<?php echo $key; ?>" data-toggle="tab"> <?php echo $title; ?> </a>
                </li>
                <?php } ?>
            </ul>
            
            <div class="tab-content">

                <div class="tab-pane active" id="tab-basics">
                
                    <div class="form-group">
                        <label class="col-md-3">Title</label>
        
                        <div class="col-md-7">
                            <input type="text" name="title" value="<?php echo $flash->old('title'); ?>" class="form-control" />
                        </div>
                        <!-- /.col -->
        
                    </div>
                    <!-- /.form-group -->
                    
                    <div class="form-group">
                        <?php if (!empty($parents)) { ?>
                        <label class="col-md-3">Parent</label>
                        <div class="col-md-7"> 
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
                <!-- /.tab-pane -->
                
                <div class="tab-pane" id="tab-permissions">
                

                
                </div>
                <!-- /.tab-pane -->
                
                <?php foreach ((array) $this->event->getArgument('content') as $key => $content ) { ?>
                <div class="tab-pane" id="tab-<?php echo $key; ?>">
                    <?php echo $content; ?>
                </div>
                <?php } ?>
                
            </div>
            <!-- /.tab-content -->
        </div>
    </div>

</form>

</div>