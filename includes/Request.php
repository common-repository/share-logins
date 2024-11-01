<?php
/**
 * All public facing functions
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
 * @subpackage Request
 * @author Nazmul Ahsan <n.mukto@gmail.com>
 */
class Request extends Hooks {

    /**
     * Constructor function
     */
    public function __construct( $plugin ) {
        $this->name         = $plugin['Name'];
        $this->site_url     = untrailingslashit( get_bloginfo( 'url' ) );
        $this->namespace    = CXSL_API_NAMESPACE;
        $this->ncrypt       = ncrypt();
        $this->access_token = cx_get_access_token();
        $this->remote_sites = cx_get_remote_sites();
    }

    public function login( $user_login, $user ) {

        if( is_user_logged_in() ) return;
        if( cx_within_route() ) return;
        if( !cx_is_role_allowed( $user ) ) return;

        cx_clean_scheduled_urls();

        $remote_sites = $this->remote_sites;
        $_scheduled_logins = array();
        foreach ( $remote_sites as $site ) {

            if( cx_config_is_enabled( 'outgoing', $site, 'login' ) ) :
            
            $url = "{$site}/?rest_route=/{$this->namespace}/login";
            $args = array(
                'access_token'  => $this->access_token,
                'site_url'      => $this->site_url,
                'user_login'    => $this->ncrypt->encrypt( $user_login ),
                'remember'      => ( isset( $_POST['rememberme'] ) ? 1 : 0 ),
            );
            
            $_scheduled_url                 = add_query_arg( 'token', $this->ncrypt->encrypt( json_encode( $args ) ), $url );
            $_scheduled_logins[ $site ]     = $_scheduled_url;

            cx_add_log( 'login', 'outgoing', $user_login, cx_get_route_home( $url ) );

            endif;
        }
        cx_set_scheduled_urls( $_scheduled_logins );

    }

    public function logout() {

        if( !is_user_logged_in() ) return;
        if( cx_within_route() ) return;

        $user   = wp_get_current_user();
        if( !cx_is_role_allowed( $user ) ) return;

        cx_clean_scheduled_urls();

        $remote_sites = $this->remote_sites;

        $_scheduled_logouts = array();

        foreach ( $remote_sites as $site ) {

            if( cx_config_is_enabled( 'outgoing', $site, 'logout' ) ) :
            
            $url = "{$site}/?rest_route=/{$this->namespace}/logout";

            $args = array(
                'access_token'  => $this->access_token,
                'site_url'      => $this->site_url,
                'user_login'    => $this->ncrypt->encrypt( $user->user_login ),
            );

            $_scheduled_url                 = add_query_arg( 'token', $this->ncrypt->encrypt( json_encode( $args ) ), $url );
            $_scheduled_logouts[ $site ]    = $_scheduled_url;

            cx_add_log( 'logout', 'outgoing', $user->user_login, cx_get_route_home( $url ) );

            endif;

        }
        cx_set_scheduled_urls( $_scheduled_logouts );
    }
}