<?php
/**
 * All AJAX related functions
 */
namespace codexpert\Image_Sizes;
use codexpert\product\Base;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage AJAX
 * @author codexpert <hello@codexpert.io>
 */
class AJAX extends Base {

	public $plugin;

	/**
	 * Constructor function
	 */
	public function __construct( $plugin ) {
		$this->plugin	= $plugin;
		$this->slug		= $this->plugin['TextDomain'];
		$this->name		= $this->plugin['Name'];
		$this->version	= $this->plugin['Version'];
	}

	/**
	 * Regenerate thumbnails
	 *
	 * @since 3.0
	 */
	public function regen_thumbs() {

		$response = [
			'status'	=> 0,
			'message'	=> __( 'Failed', 'image-sizes' ),
		];

		if( !wp_verify_nonce( $_GET['_nonce'], $this->slug ) ) {
			$response['message'] = __( 'Unauthorized', 'image-sizes' );
			wp_send_json( $response );
		}

		global $wpdb;

		$images = $wpdb->get_results( "SELECT `ID` FROM `$wpdb->posts` WHERE `post_type` = 'attachment' AND `post_mime_type` LIKE 'image/%'" );

		$images_handled = $thumbs_deleted = $thumbs_created = 0;

		foreach ( $images as $image ) {
			$image_id = $image->ID;
			$main_img = get_attached_file( $image_id );

			// remove old thumbnails first
			$old_metadata = wp_get_attachment_metadata( $image_id );
			$thumb_dir = dirname( $main_img ) . DIRECTORY_SEPARATOR;
			foreach ( $old_metadata['sizes'] as $old_size => $old_size_data ) {
				wp_delete_file( $thumb_dir . $old_size_data['file'] );
				$thumbs_deleted++;
			}

			// generate new thumbnails
			if ( false !== $main_img && file_exists( $main_img ) ) {
				$new_thumbs = wp_generate_attachment_metadata( $image_id, $main_img );
				wp_update_attachment_metadata( $image_id, $new_thumbs );
				$thumbs_created += count( $new_thumbs['sizes'] );
			}
			$images_handled++;
		}

		$response['status'] 	= 1;
		$response['message'] 	= '<p id="cxis-handled"><span class="dashicons dashicons-yes"></span>' . sprintf( __( '%d images processed.', 'image-sizes' ), $images_handled ) . '</p>';
		$response['message'] 	.= '<p id="cxis-deleted"><span class="dashicons dashicons-yes"></span>' . sprintf( __( '%d thumbnails removed.', 'image-sizes' ), $thumbs_deleted ) . '</p>';
		$response['message'] 	.= '<p id="cxis-created"><span class="dashicons dashicons-yes"></span>' . sprintf( __( '%d thumbnails regenerated.', 'image-sizes' ), $thumbs_created ) . '</p>';
		$response['counter'] 	= [
			'handled'	=> $images_handled,
			'deleted'	=> $thumbs_deleted,
			'created'	=> $thumbs_created,
		];

		wp_send_json( $response );
	}

	public function dismiss_notice() {

		$response = [
			'status'	=> 0,
			'message'	=> __( 'Failed', 'image-sizes' ),
		];

		if( !wp_verify_nonce( $_GET['_nonce'], $this->slug ) ) {
			$response['message'] = __( 'Unauthorized', 'image-sizes' );
			wp_send_json( $response );
		}

		update_option( $_GET['meta_key'], 1 );
		wp_send_json( [] );
	}

}