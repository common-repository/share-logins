<?php 

$_remote_sites = cx_get_remote_sites(); ?>
<div class="cx-remote-sites-validation-area">
	<h4><?php _e( 'Remote Sites', 'share-logins' ); ?></h4>
	<ul>
		<?php foreach ( $_remote_sites as $_site ): ?>
			<li>
				<button data-remote_site='<?php echo $_site; ?>' class="cx-btn cx-btn-validate"><?php _e( 'Validate', 'share-logins' ) ?></button>
				<span class="cx-remote-sites-link"><?php echo $_site; ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
</div>