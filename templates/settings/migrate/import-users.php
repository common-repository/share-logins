<?php
$form_id = function_exists( 'cx_is_active' ) ? 'import-users' : 'import-users-lite';
?>
<form action="" id="<?php echo $form_id; ?>" class="migrate-users">
	<?php wp_nonce_field(); ?>
	<input type="hidden" name="action" value="import-users">
	<p id="row-csv">
		<label for="csv"><?php _e( 'Choose File', 'share-logins' ); ?></label>
		<input type="file" name="csv" id="csv">
		<small><?php _e( 'Choose the file that was exported from another site. The file should have <code>.cx</code> extension', 'share-logins' ); ?></small>
	</p>
	<p id="row-replace">
		<label for="replace"><?php _e( 'Update Users', 'share-logins' ); ?></label>
		<input type="checkbox" name="replace" id="replace">
		<small><?php _e( 'Check this if you prefer to update existing users.', 'share-logins' ); ?></small>
	</p>
	<p id="row-remove_role" style="display: none;">
		<label for="remove_role"><?php _e( 'Remove Existing Roles', 'share-logins' ); ?></label>
		<input type="checkbox" name="remove_role" id="remove_role">
		<small><?php _e( 'If checked, a user\'s existing roles will be removed and new ones from the file will be added. (Not recommended)', 'share-logins' ); ?></small>
	</p>
	<p id="row-submit" class="submit">
		<input type="submit" class="button button-primary cx-submit" value="<?php _e( 'Import Users', 'share-logins' ); ?>">
	</p>
	<?php if( !function_exists( 'cx_is_active' ) ) : ?>
	<div class="cx-overlay">
		<div class="cx-pro-text">
			<h2><span class="dashicons dashicons-lock"></span> <?php _e( 'Pro Feature', 'share-logins' ); ?></h2>
			<a href="<?php echo admin_url( 'admin.php?page=share-logins#share-logins_upgrade' ); ?>" class="button button-primary see-pro"><?php _e( 'Upgrade to unlock', 'share-logins' ); ?></a>
		</div>
	</div>
	<?php endif; ?>
</form>