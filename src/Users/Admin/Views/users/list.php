<div class="row">
	<div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
		<h1 class="page-title txt-color-blueDark">
			<i class="fa fa-table fa-fw "></i> 
				Users 
			<span> > 
				List
			</span>
		</h1>
	</div>
	<div class="col-xs-12 col-sm-5 col-md-5 col-lg-8">
        <ul id="sparks" class="list-actions list-unstyled list-inline">
            <li>
                <a class="btn btn-success" href="./admin/user/create">Add New</a>
            </li>
        </ul>            	
	</div>
</div>

<form id="list-form" action="./admin/users" method="post">

    <div class="no-padding">
    
        <div class="row">
            <div class="col-xs-12 col-sm-5 col-md-5 col-lg-8">

                <ul class="list-filters list-unstyled list-inline">
                    <li>
                        <select id="group_filter" name="filter[group]" class="form-control" onchange="this.form.submit();">
                            <option value="">All Groups</option>
                            <?php foreach (\Users\Models\Groups::find() as $group) : ?>
                                <option <?php if($state->get('filter.group') == $group->id) { echo 'selected'; } ?> value="<?php echo $group->_id; ?>"><?php echo $group->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </li>                
                </ul>    

            </div>
            <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
                <div class="form-group">
                    <div class="input-group">
                        <input class="form-control" type="text" name="filter[keyword]" placeholder="Search..." maxlength="200" value="<?php echo $state->get('filter.keyword'); ?>"> 
                        <span class="input-group-btn">
                            <input class="btn btn-primary" type="submit" onclick="this.form.submit();" value="Search" />
                            <button class="btn btn-danger" type="button" onclick="Dsc.resetFormFilters(this.form);">Reset</button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <ul class="list-filters list-unstyled list-inline">
                    <li>
                        <select name="list[order]" class="form-control" onchange="this.form.submit();">
                            <option value="last_visit.time" <?php if ($state->get('list.order') == 'last_visit.time') { echo "selected='selected'"; } ?>>Last Visit</option>
                            <option value="metadata.created.time" <?php if ($state->get('list.order') == 'metadata.created.time') { echo "selected='selected'"; } ?>>Registered</option>
                        </select>
                    </li>
                    <li>
                        <select name="list[direction]" class="form-control" onchange="this.form.submit();">
                            <option value="1" <?php if ($state->get('list.direction') == '1') { echo "selected='selected'"; } ?>>ASC</option>
                            <option value="-1" <?php if ($state->get('list.direction') == '-1') { echo "selected='selected'"; } ?>>DESC</option>
                        </select>                        
                    </li>
                </ul>            
            </div>
            
            <div class="col-xs-12 col-sm-6">
                <div class="text-align-right">
                <ul class="list-filters list-unstyled list-inline">
                    <li>
                        <?php if (!empty($paginated->items)) { ?>
                        <?php echo $paginated->getLimitBox( $state->get('list.limit') ); ?>
                        <?php } ?>
                    </li>                
                </ul>    
                </div>
            </div>
        </div>
        
        <div class="widget-body-toolbar">    
    
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-lg-3">
                    <span class="pagination">
                    <div class="input-group">
                        <select id="bulk-actions" name="bulk_action" class="form-control">
                            <option value="null">-Bulk Actions-</option>
                            <option value="delete" data-action="./admin/users/delete">Delete</option>
                        </select>
                        <span class="input-group-btn">
                            <button class="btn btn-default bulk-actions" type="button" data-target="bulk-actions">Apply</button>
                        </span>
                    </div>
                    </span>
                </div>    
                <div class="col-xs-12 col-sm-6 col-lg-6 col-lg-offset-3">
                    <div class="text-align-right">
                        <?php if (!empty($paginated->total_pages) && $paginated->total_pages > 1) { ?>
                            <?php echo $paginated->serve(); ?>
                        <?php } ?>
                    </div>            
                </div>
            </div>
        
        </div>
        <!-- /.widget-body-toolbar -->
        
        <?php if (!empty($paginated->items)) { ?>
    
            <?php foreach($paginated->items as $item) { ?>
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-2 col-md-1">
                            <input type="checkbox" class="icheck-input" name="ids[]" value="<?php echo $item->id; ?>">
                        </div>
                        <div class="col-xs-10 col-md-4">
                            <h4>
                                <a href="./admin/user/edit/<?php echo $item->id; ?>">
                                    <?php echo $item->fullName(); ?>
                                </a>
                            </h4>
                            <div>
                                <a href="./admin/user/edit/<?php echo $item->id; ?>">
                                    <?php echo $item->email; ?>
                                </a>
                            </div>
                            <div>
                                <label>Username:</label> <?php echo $item->username; ?>
                            </div>                            
                        </div>
                        <div class="col-xs-10 col-xs-offset-2 col-md-6 col-md-offset-0">
                            <div>
                                <label>Last Visit:</label> <?php echo date( 'Y-m-d', $item->{'last_visit.time'} ); ?> 
                            </div>
                            <div>
                                <label>Registered:</label> <?php echo date( 'Y-m-d', $item->{'metadata.created.time'} ); ?> 
                            </div>
                            <div>
                                <?php echo implode(", ", \Joomla\Utilities\ArrayHelper::getColumn( (array) $item->groups, 'title' ) ); ?>
                            </div>                        
                        </div>
                        <div class="hidden-xs hidden-sm col-md-1">
    	                    <a class="btn btn-xs btn-danger" data-bootbox="confirm" href="./admin/user/delete/<?php echo $item->id; ?>">
    	                        <i class="fa fa-times"></i>
    	                    </a>                        
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        
        <?php } else { ?>
                <div class="">No items found.</div>
        <?php } ?>
        
        <div class="dt-row dt-bottom-row">
            <div class="row">
                <div class="col-sm-10">
                    <?php if (!empty($paginated->total_pages) && $paginated->total_pages > 1) { ?>
                        <?php echo $paginated->serve(); ?>
                    <?php } ?>
                </div>
                <div class="col-sm-2">
                    <div class="datatable-results-count pull-right">
                        <span class="pagination">
                            <?php echo (!empty($paginated->total_pages)) ? $paginated->getResultsCounter() : null; ?>
                        </span>
                    </div>
                </div>        
            </div>
        </div>
    
    </div>
    <!-- /.no-padding -->
    
</form>