<?php
global $wp_roles;
$roles = $wp_roles->get_names();
$meta_keys = cx_user_meta_keys();

$form_id = function_exists( 'cx_is_active' ) ? 'export-users' : 'export-users-lite';
?>
<form action="" id="<?php echo $form_id; ?>" class="migrate-users">
	<?php wp_nonce_field(); ?>
	<input type="hidden" name="action" value="export-users">
	<p id="row-exclude">
		<label for="exclude"><?php _e( 'Exclude Users', 'share-logins' ); ?></label>
		<input type="text" name="exclude" id="exclude" placeholder="<?php _e( 'Example: 23, 4, 11, 455, 97', 'share-logins' ); ?>">
		<small><?php _e( 'Input user ID\'s that you don\'t want to export. Separate ID\'s with comma.', 'share-logins' ); ?></small>
	</p>
	<p id="row-role__not_in">
		<label for="role__not_in"><?php _e( 'Exclude Roles', 'share-logins' ); ?></label>
		<select name="role__not_in[]" id="role__not_in" class="multi-chosen cx-chosen" multiple>
			<?php
			foreach ( $roles as $name => $label ) {
				echo "<option value='{$name}'>{$label}</option>";
			}
			?>
		</select>
		<small class="cx-desc"><?php _e( 'Choose user roles that you don\'t want to export users from.', 'share-logins' ); ?></small>
	</p>
	<p id="row-meta_keys">
		<label for="meta_keys"><?php _e( 'Meta Fields', 'share-logins' ); ?></label>
		<select name="meta_keys[]" id="meta_keys" class="multi-chosen cx-chosen" multiple>
			<?php
			foreach ( $meta_keys as $meta_key ) {
				$_selected = in_array( $meta_key, array( 'nickname', 'first_name', 'last_name' ) ) ? 'selected' : '';
				echo "<option value='{$meta_key}' {$_selected}>{$meta_key}</option>";
			}
			?>
		</select>
		<small class="cx-desc"><?php _e( 'Select meta data that you want to export for users.', 'share-logins' ); ?></small>
	</p>
	<p id="row-submit" class="submit">
		<input type="submit" class="button button-primary cx-submit" value="<?php _e( 'Export Users', 'share-logins' ); ?>">
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