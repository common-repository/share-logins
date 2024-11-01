<table class="form-table">
	<tbody>
		<tr class="cx-remote-sites">
			<th scope="row">
				<label for="share-logins_remote_sites[]"><?php _e( 'Remote Sites', 'share-logins' ); ?></label>
			</th>
			<td>
				<?php
				$_is_free = !function_exists( 'cx_is_active' ) ? 'cx-free' : '';
				$_remote_sites = cx_get_remote_sites();
				foreach ( $_remote_sites as $_site ) :
				echo "
				<p class='cx-site-row'>
					<button type='button' class='cx-btn cx-btn-remove'>-</button>
					<button type='button' class='cx-btn cx-btn-add {$_is_free}'>+</button>
					<input type='url' class='cx-remote-site' name='share-logins_remote_sites[]' value='{$_site}'>
				</p>
				";
				endforeach;
				?>
				<p class="cx-site-row">
					<button type="button" class="cx-btn cx-btn-remove">-</button>
					<button type="button" class="cx-btn cx-btn-add <?php echo $_is_free; ?>">+</button>
					<input type="url" class="cx-remote-site" name="share-logins_remote_sites[]">
				</p>
			</td>
		</tr>
	</tbody>
</table>