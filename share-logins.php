<?php
/*
Plugin Name: Share Logins
Description: Share users and logins across multiple sites
Plugin URI: https://codexpert.io
Author: codexpert
Author URI: https://codexpert.io
Version: 3.0.2
Text Domain: share-logins
Domain Path: /languages
*/

/**
 * Woolementor is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * Woolementor is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 */

namespace codexpert\Share_Logins;
use codexpert\Product\Survey;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CXSL', __FILE__ );
define( 'CXSL_DEBUG', false );

/**
 * Main class for the plugin
 * @package Plugin
 * @author Nazmul Ahsan <n.mukto@gmail.com>
 */
class Plugin {
	
	public static $_instance;
	public $slug;
	public $name;
	public $version;
	public $server;
	public $required_php = '5.6';
	public $required_wp = '4.0';

	public function __construct() {
		self::define();
		
		if( !$this->_ready() ) return;

		self::includes();
		self::hooks();
	}

	/**
	 * Define constants
	 */
	public function define(){
		if( !function_exists( 'get_plugin_data' ) ) {
		    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$this->plugin = get_plugin_data( CXSL );

		$this->server = 'https://codexpert.io';

		$this->plugin['File'] = CXSL;
		$this->plugin['Server'] = $this->server;

		$this->server = 'https://my.codexpert.io';

		if ( !defined( 'CXSL_API_NAMESPACE' ) ) {
			define( 'CXSL_API_NAMESPACE', 'share-logins/v3' );
		}
	}

	/**
	 * Dependency and Version compatibility
	 */
	public function _ready() {
		$_ready = true;

		if( version_compare( get_bloginfo( 'version' ), $this->required_wp, '<' ) ) {
			add_action( 'admin_notices', function() {
				echo "
					<div class='notice notice-error'>
						<p>" . sprintf( __( '<strong>%s</strong> requires <i>WordPress version %s</i> or higher. You have <i>version %s</i> installed.', 'share-logins' ), $this->name, $this->required_wp, get_bloginfo( 'version' ) ) . "</p>
					</div>
				";
			} );

			$_ready = false;
		}

		if( version_compare( PHP_VERSION, $this->required_php, '<' ) ) {
			add_action( 'admin_notices', function() {
				echo "
					<div class='notice notice-error'>
						<p>" . sprintf( __( '<strong>%s</strong> requires <i>PHP version %s</i> or higher. You have <i>version %s</i> installed.', 'share-logins' ), $this->name, $this->required_php, PHP_VERSION ) . "</p>
					</div>
				";
			} );

			$_ready = false;
		}

		return $_ready;
	}

	/**
	 * Includes files
	 */
	public function includes(){
		require_once dirname( CXSL ) . '/vendor/autoload.php';
		require_once dirname( CXSL ) . '/includes/functions.php';
	}

	/**
	 * Hooks
	 */
	public function hooks(){
		// i18n
		add_action( 'plugins_loaded', array( $this, 'i18n' ) );

		/**
		 * Admin facing hooks
		 *
		 * To add an action, use $admin->action()
		 * To apply a filter, use $admin->filter()
		 */
		$admin = new Admin( $this->plugin );
		$admin->activate( 'install' );
		$admin->deactivate( 'uninstall' );
		$admin->action( 'wpmu_new_blog', 'mu_install', 6 );
		$admin->action( 'delete_blog', 'mu_uninstall', 2 );
		$admin->filter( 'cron_schedules', 'add_schedules' );
		$admin->action( 'admin_head', 'head' );
		$admin->action( 'admin_enqueue_scripts', 'enqueue_scripts' );
		$admin->action( 'cx_daily_event', 'sync_docs' );
		$admin->action( 'admin_footer', 'overlay' );
		$admin->action( 'admin_bar_menu', 'admin_bar_menu', 1, 61 );

		/**
		 * Request facing hooks
		 *
		 * To add an action, use $admin->action()
		 * To apply a filter, use $admin->filter()
		 */
		$request = new Request( $this->plugin );
		$request->action( 'wp_login', 'login', 2 );
		$request->action( 'clear_auth_cookie', 'logout' );

		/**
		 * Schedule facing hooks
		 *
		 * To add an action, use $admin->action()
		 * To apply a filter, use $admin->filter()
		 */
		$schedule = new Schedule( $this->plugin );
		$schedule->action( 'wp_footer', 'run' );
		$schedule->action( 'admin_footer', 'run' );
		$schedule->action( 'login_footer', 'run' );

		/**
		 * Settings related hooks
		 *
		 * To add an action, use $settings->action()
		 * To apply a filter, use $settings->filter()
		 */
		$settings = new Settings( $this->plugin );
		$settings->action( 'init', 'init' );
		$settings->action( 'cx-settings-after-fields', 'insert_fields' );
		$settings->action( 'cx-settings-after-form', 'section_content' );
		$settings->filter( 'cx-settings-savable', 'skip_save', 3, 99 );
		$settings->filter( 'plugin_action_links_' . plugin_basename( CXSL ), 'action_settings_link' );
		
		/**
		 * AJAX facing hooks
		 *
		 * To add a hook for logged in users, use $ajax->priv()
		 * To add a hook for non-logged in users, use $ajax->nopriv()
		 */
		$ajax = new AJAX( $this->plugin );
		$ajax->priv( 'cx-validate', 'validate' );

		/**
		 * API hooks
		 *
		 * Custom REST API
		 */
		$api = new API( $this->plugin, $request );
		$api->action( 'rest_api_init', 'register_endpoints' );

		// Product related classes
		$survey 	= new Survey( $this->plugin );
	}

	/**
	 * Internationalization
	 */
	public function i18n() {
		load_plugin_textdomain( 'share-logins', false, dirname( plugin_basename( CXSL ) ) . '/languages/' );
	}

	/**
	 * Cloning is forbidden.
	 */
	private function __clone() { }

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	private function __wakeup() { }

	/**
	 * Instantiate the plugin
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}

Plugin::instance();