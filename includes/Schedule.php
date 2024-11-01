<?php
/**
 * All trigger facing functions
 */

namespace codexpert\Share_Logins;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * @package Plugin
 * @subpackage Schedule
 * @author Nazmul Ahsan <n.mukto@gmail.com>
 */
class Schedule extends Hooks {

    /**
     * Constructor function
     */
    public function __construct( $plugin ) {
        $this->name     = $plugin['Name'];
        $this->ncrypt   = ncrypt();
        if ( session_status() == PHP_SESSION_NONE ) {
            session_start();
        }
    }

    public function run() {
        $_scheduled_urls = cx_get_scheduled_urls();
        if( is_array( $_scheduled_urls ) && count( $_scheduled_urls ) > 0 ) :

		foreach ( $_scheduled_urls as $url ) {
            $url = urldecode( $url );
			echo "<script src='{$url}'></script>";

            if( defined( 'CXSL_DEBUG' ) && CXSL_DEBUG ) {
                @parse_str( $url );
                cx_add_log( 'trigger', 'outgoing', $this->ncrypt->decrypt( $user_login ), cx_get_route_home( $url ) );
            }

            cx_remove_scheduled_url( cx_get_route_home( $url )  );
		}

        endif;

        /**
         * @since 2.1.3
         * @author developerwil
         * @link https://wordpress.org/support/topic/sessions-need-to-be-destroyed/
         */
        if( empty( cx_get_scheduled_urls() ) && session_status() != PHP_SESSION_NONE ) {
            session_destroy();
        }
    }
}