<div class="row">
    <div class="col-md-2">
    
        <h3>Groups</h3>
                
    </div>
    <!-- /.col-md-2 -->
                
    <div class="col-md-10">

        <div class="form-group">
            <label>Search...</label>
            <input id="groups" name="__groups" value="<?php echo implode(",", (array) \Dsc\ArrayHelper::getColumn( (array) $flash->old('groups'), 'id' ) ); ?>" type="text" class="form-control" />       
        </div>
        <!-- /.form-group -->
    
    </div>
    <!-- /.col-md-10 -->
</div>
<!-- /.row -->

<script>
jQuery(document).ready(function() {
    
    jQuery("#groups").select2({
        allowClear: true, 
        placeholder: "Search...",
        multiple: true,
        minimumInputLength: 3,
        ajax: {
            url: "./admin/users/groups/forSelection",
            dataType: 'json',
            data: function (term, page) {
                return {
                    q: term
                };
            },
            results: function (data, page) {
                return {results: data.results};
            }
        }
        <?php if ($flash->old('groups')) { ?>
        , initSelection : function (element, callback) {
            var data = <?php echo json_encode( \Users\Models\Groups::forSelection( array('_id'=>array('$in'=>array_map( function($input){ return new \MongoId($input); }, \Dsc\ArrayHelper::getColumn( (array) $flash->old('groups'), 'id' ) ) ) ) ) ); ?>;
            callback(data);            
        }
        <?php } ?>
    });

});
</script>

<hr/>