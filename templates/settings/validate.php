<?php
extract( $fields );
$report 	= cx_validation_report( $remote_site );
$this_site 	= site_url();
$incoming 	= cx_validation_array_formation( $report['remote']['incoming'][$this_site], $report['site_added'] );
$outgoing 	= cx_validation_array_formation( $report['remote']['outgoing'][$this_site], $report['site_added'] );

if ( $report['site_added'] != 1 ) {
	$report['access_token'] 	 = '';
	$report['secret_key'] 		 = '';
	$report['secret_iv'] 		 = '';
	$report['remote']['license'] = '';
}

$report['remote']['incoming'] = $incoming;
$report['remote']['outgoing'] = $outgoing;
// wp_send_json( $report );
$yes = __( '<span class="dashicons dashicons-yes-alt cx-green"></span>', 'share-logins' );
$no = __( '<span class="dashicons dashicons-dismiss cx-red"></span>', 'share-logins' );
$actions = array(
	'login'		=> __( 'Login', 'share-logins' ),
	'logout'	=> __( 'Logout', 'share-logins' ),
	'create'	=> __( 'Create User', 'share-logins' ),
	'update'	=> __( 'Update User', 'share-logins' ),
	'reset'		=> __( 'Reset Password', 'share-logins' ),
	'delete'	=> __( 'Delete User', 'share-logins' ),
);
?>

<div class="cx-validate-table-hading">
	<h4><?php _e( 'Connectivity', 'share-logins' ); ?></h4>
</div>
<table class="cx-validate-setup">
	<tbody>
		<tr>
			<th colspan="2"><?php echo sprintf( __( 'Is this site (%s) added on %s', 'share-logins' ), $this_site, $remote_site ); ?></th>
			<td><?php echo $report['site_added'] == 1 ? $yes : $no; ?></td>
		</tr>
		<tr>
			<th colspan="2"><?php _e( 'Access Token matched?', 'share-logins' ); ?></th>
			<td><?php echo $report['access_token'] == 1 ? $yes : $no; ?></td>
		</tr>
		<tr>
			<th colspan="2"><?php _e( 'Secret Key matched?', 'share-logins' ); ?></th>
			<td><?php echo $report['secret_key'] == 1 ? $yes : $no; ?></td>
		</tr>
		<tr>
			<th colspan="2"><?php _e( 'Secret IV matched?', 'share-logins' ); ?></th>
			<td><?php echo $report['secret_iv'] == 1 ? $yes : $no; ?></td>
		</tr>
	</tbody>
</table>

<!-- INDIVIDUAL CONFIGURATIONS -->

<div class="cx-validate-table-hading">
	<h4><?php printf( __( 'Remote site (%s)', 'share-logins' ), $remote_site ); ?></h4>
	<?php if( function_exists( 'cx_is_pro' ) && cx_is_pro() ) echo '<p class="cs-license-status">' . sprintf( __( 'License activation: %s', 'share-logins' ), ( $report['remote']['license'] == 1 ? $yes : $no ) ) . '</p>'; ?>
</div>
<table  class="cx-configuration-remote-site">
	<tbody>
		<tr class="hading">
			<th><?php _e( 'Action', 'share-logins' ); ?></th>
			<th><?php printf( __( 'Accepts From %s', 'share-logins' ), $this_site ); ?></th>
			<th><?php printf( __( 'Sends To %s', 'share-logins' ), $this_site ); ?></th>
		</tr>
		<?php
		foreach ( $actions as $key => $label ) {
			echo "
			<tr>
				<th>{$label}</th>
				<td>" . cx_validate_message( $report, 'remote', 'incoming', $key ) . "</td>
				<td>" . cx_validate_message( $report, 'remote', 'outgoing', $key ) . "</td>
			</tr>
			";
		}
		?>
	</tbody>
</table>


<div class="cx-validate-table-hading">
	<h4><?php printf( __( 'Current site (%s)', 'share-logins' ), home_url( '' ) ); ?></h4>
	<?php if( function_exists( 'cx_is_pro' ) && cx_is_pro() ) echo '<p class="cs-license-status">' . sprintf( __( 'License activation: %s', 'share-logins' ), ( $report['local']['license'] == 1 ? $yes : $no ) ) . '</p>'; ?>
</div>
<table  class="cx-configuration-local-site">
	<tbody>
		<!-- LOCAL SITE -->
		<tr class="hading">
			<th><?php _e( 'Action', 'share-logins' ); ?></th>
			<th><?php printf( __( 'Accepts From %s', 'share-logins' ), $remote_site ) ; ?></th>
			<th><?php printf( __( 'Sends To %s', 'share-logins' ), $remote_site ) ; ?></th>
		</tr>
		<?php
		foreach ( $actions as $key => $label ) {
			echo "
			<tr>
				<th>{$label}</th>
				<td>" . cx_validate_message( $report, 'local', 'incoming', $key ) . "</td>
				<td>" . cx_validate_message( $report, 'local', 'outgoing', $key ) . "</td>
			</tr>
			";
		}
		?>
	</tbody>
</table>