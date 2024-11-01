<?php
/**
 * All admin facing functions
 */
namespace codexpert\Image_Sizes;
use codexpert\product\Base;
use codexpert\product\Wizard;
use codexpert\product\Metabox;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Admin
 * @author codexpert <hello@codexpert.io>
 */
class Admin extends Base {

	public $plugin;

	/**
	 * Constructor function
	 */
	public function __construct( $plugin ) {
		$this->plugin	= $plugin;
		$this->slug		= $this->plugin['TextDomain'];
		$this->name		= $this->plugin['Name'];
		$this->server	= $this->plugin['server'];
		$this->version	= $this->plugin['Version'];
	}

	/**
	 * Internationalization
	 */
	public function i18n() {
		load_plugin_textdomain( 'image-sizes', false, CXIS_DIR . '/languages/' );
	}
	
	public function update_cache( $post_id, $post, $update ) {
		wp_cache_delete( "image_sizes_{$post->post_type}", 'image_sizes' );
	}

	public function footer_text( $text ) {
		if(get_current_screen()->parent_base != $this->slug ) return $text;

		return sprintf( __( 'If you like <strong>%1$s</strong>, please <a href="%2$s" target="_blank">leave us a %3$s rating</a> on WordPress.org! It\'d motivate and inspire us to make the plugin even better!', 'image-sizes' ), $this->name, "https://wordpress.org/support/plugin/{$this->slug}/reviews/?filter=5#new-post", '⭐⭐⭐⭐⭐' );
	}

		/**
		 * If the plugin is re-activated
		 */
		public function reask_survey() {
			if( get_option( 'image-sizes_survey_agreed' ) != 1 ) {
				delete_option( 'image-sizes_survey' );
			}
		}

		public function set_sizes() {
			update_option( '_image-sizes', cxis_get_image_sizes() );
		}
		
		/**
		 * Enqueue JavaScripts and stylesheets
		 */
		public function enqueue_scripts() {
			$min = defined( 'CXIS_DEBUG' ) && CXIS_DEBUG ? '' : '.min';
			
			wp_enqueue_script( $this->slug, plugins_url( "/assets/js/admin{$min}.js", CXIS ), [ 'jquery' ], $this->version, true );
			wp_enqueue_style( $this->slug, plugins_url( "/assets/css/admin{$min}.css", CXIS ), '', $this->version, 'all' );
			
			$localized = array(
				'ajaxurl'	=> admin_url( 'admin-ajax.php' ),
				'nonce'		=> wp_create_nonce( $this->slug ),
				'regen'		=> __( 'Regenerate', 'image-sizes' ),
				'regening'	=> __( 'Regenerating..', 'image-sizes' ),
			);
			wp_localize_script( $this->slug, 'CXIS', apply_filters( "{$this->slug}-localized", $localized ) );
		}

		/**
	     * unset image size(s)
	     *
	     * @since 1.0
	     */
	    public function image_sizes( $sizes ){
	        $disables = image_sizes_get_option( 'prevent_image_sizes', 'disables', [] );

	        if( count( $disables ) ) :
	        foreach( $disables as $disable ){
	            unset( $sizes[ $disable ] );
	        }
	        endif;
	        
	        return $sizes;
	    }

		/**
		 * Sync docs from https://help.codexpert.io daily
		 *
		 * @since 1.0
		 */
		public static function sync_docs() {
		    $json_url = 'https://help.codexpert.io/wp-json/wp/v2/docs/?parent=1375&per_page=20';
		    if( !is_wp_error( $data = wp_remote_get( $json_url ) ) ) {
		        update_option( 'image-sizes-docs-json', json_decode( $data['body'], true ) );
		    }
		}
}