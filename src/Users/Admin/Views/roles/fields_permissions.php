<?php 
	$opts = array(
		array( 'value' => 1, 'text' => 'Allow' ),
		array( 'value' => 0, 'text' => 'Deny' ),
	);

	foreach( $resources as $name => $actions ) {
		?>
		
<div class="row">
    <div class="col-md-4">
    
        <h3><?php echo $name; ?></h3>
                
    </div>
    <!-- /.col-md-2 -->
                
    <div class="col-md-8">

   <?php 
   	foreach( $actions as $action ) {

		$val = 0;
		
		if( isset( $permissions[(string)$action] ) ) {
			$val = (int)$permissions[(string)$action];
		}
		?>

        <div class="form-group">
            <label><?php echo $acl->getActionDescription($action->getAction()); ?></label>
            <select name="set_permissions[<?php echo $action->getResource(); ?>][<?php echo $action->getAction(); ?>]" class="form-control">
	            <?php echo \Dsc\Html\Select::options( $opts, $val );?>
            </select>
        </div>
        <!-- /.form-group -->
   	
   	<?php } ?>

	</div>
</div>
<?php
	}
?>
