<?php //echo \Dsc\Debug::dump( $state, false ); ?>

<form id="detail-form" action="./admin/users/group/<?php echo $item->get( $model->getItemKey() ); ?>" class="form-horizontal" method="post">

    <div class="form-group">

        <label class="col-md-3">Name</label>

        <div class="col-md-7">
            <input type="text" name="name" value="<?php echo $flash->old('name'); ?>" class="form-control" />
        </div>
        <!-- /.col -->

    </div>
  

    <hr/>
    
    <div class="form-actions">

        <div>
            <div class="btn-group">
                <button type="submit" class="btn btn-primary">Save</button>
                <input id="primarySubmit" type="hidden"
                    value="save_edit" name="submitType" />
                <button type="button"
                    class="btn btn-primary dropdown-toggle"
                    data-toggle="dropdown">
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li><a
                        onclick="document.getElementById('primarySubmit').value='save_new'; document.getElementById('detail-form').submit();"
                        href="javascript:void(0);">Save & Create Another</a>
                    </li>
                    <li><a
                        onclick="document.getElementById('primarySubmit').value='save_close'; document.getElementById('detail-form').submit();"
                        href="javascript:void(0);">Save & Close</a></li>
                </ul>
            </div>
            &nbsp; <a class="btn btn-default" href="./admin/users">Cancel</a>
        </div>

    </div>
    <!-- /.form-group -->

</form>
