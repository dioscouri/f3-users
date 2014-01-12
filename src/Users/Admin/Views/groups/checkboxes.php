<?php if (!empty($groups)) { ?>
<div class="max-height-200 list-group-item">
	<?php $current = \Joomla\Utilities\ArrayHelper::getColumn( (array) $flash->old('groups'), 'id' ); ?>
    <?php foreach ($groups as $one) { ?>
    <div class="checkbox">
        <label>
            <input type="checkbox" name="groups[]" class="icheck-input" value="<?php echo $one->_id; ?>" <?php if (in_array($one->_id, $current)) { echo "checked='checked'"; } ?>>
            <?php echo $one->name;  ?>
        </label>
    </div>
    <?php } ?> 
    
</div>
<?php } ?>