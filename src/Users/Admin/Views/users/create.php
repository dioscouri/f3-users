
<form id="detail-form" action="./admin/user" class="form-horizontal" method="post">
    <div class="row">
        <div class="col-md-9">
            <div class="form-group">

        <label class="col-md-3">Username</label>

        <div class="col-md-7">
            <input type="text" name="username"
                value="<?php echo $flash->old('username'); ?>"
                class="form-control" />
        </div>
        <!-- /.col -->

    </div>
    <!-- /.form-group -->

    <div class="form-group">

        <label class="col-md-3">First Name</label>

        <div class="col-md-7">
            <input type="text" name="first_name"
                value="<?php echo $flash->old('first_name'); ?>"
                class="form-control" />
        </div>
        <!-- /.col -->

    </div>
    <!-- /.form-group -->

    <div class="form-group">

        <label class="col-md-3">Last Name</label>

        <div class="col-md-7">
            <input type="text" name="last_name"
                value="<?php echo $flash->old('last_name'); ?>"
                class="form-control" />
        </div>
        <!-- /.col -->

    </div>
    <!-- /.form-group -->

    <div class="form-group">

        <label class="col-md-3">Email Address</label>

        <div class="col-md-7">
            <input type="text" name="email"
                value="<?php echo $flash->old('email'); ?>"
                class="form-control" />
        </div>
        <!-- /.col -->

    </div>
    <!-- /.form-group -->

<hr />

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


        </div>
        <div class="col-md-3">

            
            
            <div class="portlet">

                <div class="portlet-header">

                    <h3>Groups</h3>

                </div>
                <!-- /.portlet-header -->

                <div class="">
                    <div id="groups" class="list-group">
                        <div id="groups-checkboxes">
                        <?php echo $this->renderLayout('groups/checkboxes.php'); ?>
                        </div>
                
                        <div class="list-group-item">
                            <script>
                            Dsc.refreshCategories = function(r) {

                                var form_data = new Array();
                                jQuery.merge( form_data, jQuery('#groups-checkboxes').find(':input').serializeArray() );
                                jQuery.merge( form_data, [{ name: "groups_ids[]", value: r.result._id['$id'] }] );

                                var request = jQuery.ajax({
                                    type: 'post', 
                                    url: './admin/users/groups/checkboxes',
                                    data: form_data
                                }).done(function(data){
                                    var lr = jQuery.parseJSON( JSON.stringify(data), false);
                                    if (lr.result) {
                                        jQuery('#groups-checkboxes').html(lr.result);
                                        App.initICheck();
                                    }
                                });
                            }
                            </script>
                                                    
                            <div data-toggle="collapse" data-target="#addCategoryForm" class="btn btn-link">
                                Add New Group
                            </div>
                            <div id="addCategoryForm" class="collapse">
                                <div class="panel-body">
                                    
                                    <div id="quick-form" action="./admin/users/group" data-callback="Dsc.refreshCategories" data-message_container="quick-form-response-container">
                                    
                                    <div id="quick-form-response-container"></div>
                                    
                                    <div class="form-group">
                                        <input type="text" name="name" placeholder="Name" class="form-control" />
                                    </div>
                                    <!-- /.form-group -->
                                    
                                    <div id="parents" class="form-group">
                                        <?php echo $this->renderLayout('categories/list_parents.php'); ?>                    
                                    </div>
                                    <!-- /.form-group -->        
                    
                                    <hr />
                    
                                    <div class="form-actions">
                    
                                        <div>
                                            <button type="button" class="btn btn-primary dsc-ajax-submit" data-target="quick-form">Create</button>
                                        </div>
                    
                                    </div>
                                    <!-- /.form-group -->
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.portlet-content -->

            </div>
            
 
        </div>
    </div>
</form>
