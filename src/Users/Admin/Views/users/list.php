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

    <input type="hidden" name="list[order]" value="<?php echo $state->get('list.order'); ?>" />
    <input type="hidden" name="list[direction]" value="<?php echo $state->get('list.direction'); ?>" />
        
    <div class="row">
        <div class="col-xs-12 col-sm-5 col-md-5 col-lg-8">
            <ul class="list-filters list-unstyled list-inline">
                <li>
                    <a class="btn btn-link" href="javascript:void(0);" onclick="ShopToggleAdvancedFilters();">Advanced Filters</a>
                </li>
                <li>
                    <select id="group_filter" name="filter[group]" class="form-control" onchange="this.form.submit();">
                        <option value="">All Groups</option>
                        <?php foreach (\Users\Models\Groups::find() as $group) : ?>
                            <option <?php if($state->get('filter.group') == $group->id) { echo 'selected'; } ?> value="<?php echo $group->_id; ?>"><?php echo $group->title; ?></option>
                        <?php endforeach; ?>
                    </select>
                </li>
                <li>
                </li>                
            </ul>        
        </div>
        <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
            <div class="form-group">
                <div class="input-group">
                    <input class="form-control" type="text" name="filter[keyword]" placeholder="Search..." maxlength="200" value="<?php echo $state->get('filter.keyword'); ?>"> 
                    <span class="input-group-btn">
                        <input class="btn btn-primary" type="submit" onclick="this.form.submit();" value="Search" />
                        <button class="btn btn-danger" type="button" onclick="Dsc.resetFormFilters(this.form);">Reset Filters</button>
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <div id="advanced-filters" class="panel panel-default" 
    <?php 
    if (!$state->get('filter.last_modified_after')
        && !$state->get('filter.last_modified_before')
        && !$state->get('filter.admin_tags')          
        && !$state->get('filter.last_visit_after')
        && !$state->get('filter.last_visit_before')
        && !$state->get('filter.created_after')
        && !$state->get('filter.created_before')  
    ) { ?>
        style="display: none;"
    <?php } ?>
    >
        <div class="panel-body">
            <div class="row">
                <div class="col-md-10">
                
                    <div class="row">
                        <div class="col-md-2">
                            <h4>Last Visited</h4>
                        </div>
                        <div class="col-md-10">
                            <div class="form-group">
                                <div class="input-daterange input-group" id="datepicker">
                                    <input type="text" name="filter[last_visit_after]" value="<?php echo $state->get('filter.last_visit_after'); ?>" class="input-sm ui-datepicker form-control" data-date-format="yyyy-mm-dd" data-date-today-highlight="true" data-date-today-btn="true" />
                                    <span class="input-group-addon">to</span>
                                    <input type="text" name="filter[last_visit_before]" value="<?php echo $state->get('filter.last_visit_before'); ?>" class="input-sm ui-datepicker form-control" data-date-format="yyyy-mm-dd" data-date-today-highlight="true" data-date-today-btn="true" />
                                </div>
                            </div>
                        </div>                
                    </div>                
                    
                    <div class="row">
                        <div class="col-md-2">
                            <h4>Admin Tags</h4>
                        </div>
                        <div class="col-md-10">
                            <div class="form-group">
                                <select name="filter[admin_tags]" class="form-control ui-select2" multiple>
                                    <option value="--" <?php if (in_array( '--', (array) $state->get('filter.admin_tags'))) { echo 'selected'; } ?> >Untagged</option>
                                    <?php foreach (\Users\Models\Users::distinctAdminTags() as $tag) { ?>
                                        <option <?php if (in_array( $tag, (array) $state->get('filter.admin_tags'))) { echo 'selected'; } ?> value="<?php echo $tag; ?>"><?php echo $tag; ?></option>
                                    <?php } ?>
                                </select>               
                            </div> 
                        </div>                
                    </div>                    
                    
                    <div class="row">
                        <div class="col-md-2">
                            <h4>Social Profile</h4>
                        </div>
                        <div class="col-md-10">
                            <div class="form-group">
                                <select id="social_filter" name="filter[social-profile]" class="form-control">
                                    <option value="">All Social Profiles</option>
                                    <?php 
                                    	$providers = \Users\Models\Settings::fetch()->enabledSocialProviders();
                                    	foreach ( (array)$providers as $network) : ?>
                                        <option <?php if($state->get('filter.social-profile') == $network ) { echo 'selected'; } ?> value="<?php echo $network; ?>"><?php echo $network; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>                
                    </div>
                                        
                    <div class="row">
                        <div class="col-md-2">
                            <h4>Joined</h4>
                        </div>
                        <div class="col-md-10">
                            <div class="form-group">
                                <div class="input-daterange input-group" id="datepicker">
                                    <input type="text" name="filter[created_after]" value="<?php echo $state->get('filter.created_after'); ?>" class="input-sm ui-datepicker form-control" data-date-format="yyyy-mm-dd" data-date-today-highlight="true" data-date-today-btn="true" />
                                    <span class="input-group-addon">to</span>
                                    <input type="text" name="filter[created_before]" value="<?php echo $state->get('filter.created_before'); ?>" class="input-sm ui-datepicker form-control" data-date-format="yyyy-mm-dd" data-date-today-highlight="true" data-date-today-btn="true" />
                                </div>
                            </div>
                        </div>                
                    </div>
                    
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary pull-right">Go</button>
                </div>
            </div>   
        </div> 
    </div>
    
    <script>
    ShopToggleAdvancedFilters = function(el) {
        var filters = jQuery('#advanced-filters');
        if (filters.is(':hidden')) {
            filters.slideDown();        
        } else {
        	filters.slideUp();
        }
    }
    </script>
    
    <?php if (!empty($paginated->items)) { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
        
            <div class="row">
                <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
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
                  
                <div class="col-xs-8 col-sm-5 col-md-5 col-lg-6">
                    <?php if (!empty($paginated->total_pages) && $paginated->total_pages > 1) { ?>
                        <?php echo $paginated->serve(); ?>
                    <?php } ?>            
                </div>
                
                <?php if (!empty($paginated->items)) { ?>
                <div class="col-xs-4 col-sm-3 col-md-3 col-lg-3 text-align-right">
                    <span class="pagination">
                        <span class="hidden-xs hidden-sm">
                            <?php echo $paginated->getResultsCounter(); ?>
                        </span>
                    </span>
                    <span class="pagination">
                        <?php echo $paginated->getLimitBox( $state->get('list.limit') ); ?>
                    </span>                                        
                </div>
                <?php } ?>        
                
            </div>            
            
        </div>
        <div class="panel-body">
            <div class="list-group-item">
                <div class="row">
                    <div class="col-xs-2 col-md-1">
                        <input type="checkbox" class="icheck-toggle icheck-input" data-target="icheck-id">
                    </div>
                    <div class="col-xs-10 col-md-5" data-sortable="last_name">
                        <b>Customer</b>
                    </div>
                    <div class="col-md-2" data-sortable="metadata.created.time">
                        <b>Joined</b>
                    </div>                    
                    <div class="col-md-2" data-sortable="last_visit.time">
                        <b>Last Visit</b>
                    </div>
                    <div class="hidden-xs hidden-sm col-md-2">
                        
                    </div>
                </div>
            </div>            
        
            <?php foreach($paginated->items as $item) { ?>
            <div class="list-group-item">
                
                <div class="row">
                    <div class="col-xs-2 col-md-1">
                        <input type="checkbox" class="icheck-input icheck-id" name="ids[]" value="<?php echo $item->id; ?>">
                    </div>
                    <div class="col-xs-10 col-md-5">
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
                        <?php if ($item->groups) { ?>
                        <div class="">
                            <label>Groups:</label> 
                            <span class='label label-default'><?php echo implode("</span> <span class='label label-default'>", \Dsc\ArrayHelper::getColumn( (array) $item->groups, 'title' ) ); ?></span>
                        </div>
                        <?php } ?>
                                                
                        <?php if ($item->role) { ?>
                        <div>
                            <label>Role:</label> <span class='label label-info'><?php echo $item->role ? $item->role : 'None'; ?></span>
                        </div>
                        <?php } ?>
                        
                        <?php
                        $keys = array_keys( (array) $item->{'social'} ); 
                        if (!empty($keys)) {  
                        ?>
                        <div>
                            <label>Profiles:</label> <span class='label label-success'><?php echo implode("</span> <span class='label label-success'>", $keys); ?></span>
                        </div>
                        <?php } ?>
                                                
                    </div>
                    <div class="col-md-2">
                        <a href="./admin/user/edit/<?php echo $item->id; ?>">
                            <?php echo date( 'Y-m-d', $item->{'metadata.created.time'} ); ?>
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="./admin/user/edit/<?php echo $item->id; ?>">
                             <?php echo $item->{'last_visit.time'} ? date( 'Y-m-d', $item->{'last_visit.time'} ) : 'Never Visited'; ?>
                        </a>                        
                    </div>
                    <div class="hidden-xs hidden-sm col-md-2">
                        <span class="pull-right">
    	                    <a class="btn btn-xs btn-danger" data-bootbox="confirm" href="./admin/user/delete/<?php echo $item->id; ?>">
    	                        <i class="fa fa-times"></i>
    	                    </a>
	                    </span>
                    </div>
                </div>
                
            </div>
            <?php } ?>
            
        </div>
            
        <div class="panel-footer">
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
            
    <?php } else { ?>
        <div class="list-group-item">
            No items found.
        </div>
    <?php } ?>

    </div>

</form>
