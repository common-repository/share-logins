<?php
$type = 'incoming';
?>
<table class="form-table">
	<thead>
		<tr>
			<th></th>
			<th><?php _e( 'Select All', 'share-logins' ); ?></th>
			<th><?php _e( 'Login', 'share-logins' ); ?></th>
			<th><?php _e( 'Logout', 'share-logins' ); ?></th>
			<th><?php _e( 'Create User', 'share-logins' ); ?></th>
			<th><?php _e( 'Update User', 'share-logins' ); ?></th>
			<th><?php _e( 'Reset Password', 'share-logins' ); ?></th>
			<th><?php _e( 'Delete User', 'share-logins' ); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
	$_remote_sites = cx_get_remote_sites();
	$_pro_msg = '<a class="see-pro" href="#share-logins_upgrade">' . __( 'Pro Feature', 'share-logins' ) . '</a>';
	foreach ( $_remote_sites as $url ) {
		echo "
		<tr>
			<td>{$url}</td>";
			$actions = array( 'all', 'login', 'logout', 'create-user', 'update-user', 'reset-password', 'delete-user' );
			foreach ( $actions as $action ) {
				$_class = $action == 'all' ? "check-all" : "check-this";
				$_checked = cx_config_is_enabled( $type, $url, $action ) ? 'checked' : '';

				if( cx_is_pro() || in_array( $action, array( 'all', 'login', 'logout' ) ) ) {
					echo "<td><input type='checkbox' name='share-logins_config_{$type}[{$url}][{$action}]' class='{$_class}' {$_checked}/></td>";
				}
				else {
					echo "<td><input type='checkbox' name='share-logins_config_{$type}[{$url}][{$action}]' class='' {$_checked} disabled/>{$_pro_msg}</td>";
				}
			}
		echo "
		</tr>
		";
	}
	?>
	</tbody>
</table>
<!-- 
<div style="padding-left: 10px">
	<p class="submit"><input name="submit" id="submit" class="button button-primary" value="Save Changes" type="submit"></p>
</div> -->