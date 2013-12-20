<?php //echo \Dsc\Debug::dump( $state, false ); ?>
<?php //echo \Dsc\Debug::dump( $list ); ?>

<form id="list-form" action="./admin/users/groups" method="post">

    <div class="row datatable-header">
        <div class="col-sm-6">
            <div class="row row-marginless">
                <?php if (!empty($list['subset'])) { ?>
                <div class="col-sm-4">
                    <?php echo $pagination->getLimitBox( $state->get('list.limit') ); ?>
                </div>
                <?php } ?>
				<?php if (!empty($list['count']) && $list['count'] > 1) { ?>                                
                <div class="col-sm-8">
                    <?php echo $pagination->serve(); ?>
                </div>
                <?php } ?>
            </div>
        </div>    
        <div class="col-sm-6">
            <div class="input-group">
                <input class="form-control" type="text" name="filter[keyword]" placeholder="Keyword" maxlength="200" value="<?php echo $state->get('filter.keyword'); ?>"> 
                <span class="input-group-btn">
                    <input class="btn btn-primary" type="submit" onclick="this.form.submit();" value="Search" />
                    <button class="btn btn-danger" type="button" onclick="Dsc.resetFormFilters(this.form);">Reset</button>
                </span>
            </div>
        </div>
    </div>
    
    <input type="hidden" name="list[order]" value="<?php echo $state->get('list.order'); ?>" />
    <input type="hidden" name="list[direction]" value="<?php echo $state->get('list.direction'); ?>" />

    <div class="row table-actions">
        <div class="col-md-6 col-lg-4 input-group">
            <select id="bulk-actions" name="bulk_action" class="form-control">
                <option value="null">-Bulk Actions-</option>
                <option value="delete" data-action="./admin/users/delete">Delete</option>
            </select>
            <span class="input-group-btn">
                <button class="btn btn-default bulk-actions" type="button" data-target="bulk-actions">Apply</button>
            </span>
        </div>
    </div>
    
    <div class="table-responsive datatable">
    
    <table class="table table-striped table-bordered table-hover table-highlight table-checkable">
		<thead>
			<tr>
				<th class="checkbox-column"><input type="checkbox" class="icheck-input"></th>
                <th data-sortable="name">Name</th>
                <th data-sortable="tbd">tbd</th>
                <th>tbd</th>
                <th data-sortable="tbd1">tbd</th>
                <th></th>
            </tr>
			<tr class="filter-row">
				<th></th>
                <th>
                    <input placeholder="name" name="filter[name-contains]" value="<?php echo $state->get('filter.name-contains'); ?>" type="text" class="form-control input-sm">
                </th>
                <th>
                    
                </th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
		</thead>
		<tbody>    
        
        <?php if (!empty($list['subset'])) { ?>
    
            <?php foreach ($list['subset'] as $item) { ?>
                <tr>
	                <td class="checkbox-column">
	                    <input type="checkbox" class="icheck-input" name="ids[]" value="<?php echo $item->id; ?>">
	                </td>                
                    <td class="">
                    	<h5>
                        <a href="./admin/users/group/<?php echo $item->id; ?>">
                            <?php echo $item->name; ?>
                        </a>
                        </h5>
                    </td>
                    <td class="">
                        <?php //echo $item->email; ?>
                    </td>
                    <td class="">
                        <?php //echo $item->first_name; ?>
                    </td>
                    <td class="">
                        <?php //echo $item->last_name; ?>
                    </td>
                    <td class="text-center">
                        <a class="btn btn-xs btn-secondary" href="./admin/users/group/<?php echo $item->id; ?>/edit">
                            <i class="fa fa-pencil"></i>
                        </a>
	                    &nbsp;
	                    <a class="btn btn-xs btn-danger" data-bootbox="confirm" href="./admin/users/group/<?php echo $item->id; ?>/delete">
	                        <i class="fa fa-times"></i>
	                    </a>
                    </td>
                </tr>
            <?php } ?>
        
        <?php } else { ?>
            <tr>
            <td colspan="100">
                <div class="">No items found.</div>
            </td>
            </tr>
        <?php } ?>

        </tbody>
    </table>
    
    </div>
    
    <div class="row datatable-footer">
        <?php if (!empty($list['count']) && $list['count'] > 1) { ?>
        <div class="col-sm-10">
            <?php echo (!empty($list['count']) && $list['count'] > 1) ? $pagination->serve() : null; ?>
        </div>
        <?php } ?>
        <div class="col-sm-2 pull-right">
            <div class="datatable-results-count pull-right">
            <?php echo $pagination ? $pagination->getResultsCounter() : null; ?>
            </div>
        </div>
    </div>    
    
</form>