<?php
/**
 * All API facing functions
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
 * @subpackage API
 * @author Nazmul Ahsan <n.mukto@gmail.com>
 */
class API extends Hooks {

    /**
     * Constructor function
     */
    public function __construct( $plugin, $request ) {
        $this->version      = $plugin['Version'];
        $this->request      = $request;
        $this->namespace    = CXSL_API_NAMESPACE;
        $this->ncrypt       = ncrypt();
    }

    public function register_endpoints() {
        /**
         * @endpoint base /wp-json/share-logins (not in use)
         * @endpoint /?rest_route=/share-logins
         */
        register_rest_route( $this->namespace, '/login', array(
            'methods'   => 'GET',
            'callback'  => array( $this, 'login' ),
        ) );
        register_rest_route( $this->namespace, '/logout', array(
            'methods'   => 'GET',
            'callback'  => array( $this, 'logout' ),
        ) );
        register_rest_route( $this->namespace, '/validate', array(
            'methods'   => 'GET',
            'callback'  => array( $this, 'validate' ),
        ) );
    }

    public function login( $request ) {
        $parameters = json_decode( $this->ncrypt->decrypt( $request->get_param( 'token' ) ), true );

        cx_clean_scheduled_urls();

        if( $parameters['access_token'] != cx_get_access_token() ) return;
        if( !cx_config_is_enabled( 'incoming', $parameters['site_url'], 'login' ) ) return;

        $user_login = $this->ncrypt->decrypt( $parameters['user_login'] );
        if( $user_login === false ) return;

        remove_action( 'wp_login', array( $this->request, 'login' ) );

        cx_auto_login( $user_login, $parameters['remember'] );

        cx_add_log( 'login', 'incoming', $user_login, $parameters['site_url'] );

        return $parameters;
    }

    public function logout( $request ) {
        $parameters = json_decode( $this->ncrypt->decrypt( $request->get_param( 'token' ) ), true );

        cx_clean_scheduled_urls();

        if( $parameters['access_token'] != cx_get_access_token() ) return;
        if( !cx_config_is_enabled( 'incoming', $parameters['site_url'], 'logout' ) ) return;

        $user_login = $this->ncrypt->decrypt( $parameters['user_login'] );
        if( $user_login === false ) return;

        remove_action( 'clear_auth_cookie', array( $this->request, 'logout' ) );

        wp_logout();

        cx_add_log( 'logout', 'incoming', $user_login, $parameters['site_url'] );

        return $parameters;
    }

    /**
     * Validate configuration
     * @author Nazmul Ahsan <n.mukto@gmail.com>
     * @since 1.3
     */
    public function validate( $request ) {
        $parameters = $request->get_params();
        $response = array(
            'site_added'    => in_array( $parameters['site_url'], cx_get_remote_sites() ),
            'access_token'  => $parameters['access_token'] == cx_get_access_token(),
            'secret_key'    => $parameters['secret_key'] == cx_get_secret_key(),
            'secret_iv'     => $parameters['secret_iv'] == cx_get_secret_iv(),
            'remote'        => array(
                'license'   => cx_is_pro() && function_exists( 'cx_is_active' ) && cx_is_active(),                
                'incoming' => get_option( 'share-logins_config_incoming' ),
                'outgoing' => get_option( 'share-logins_config_outgoing' ),
            )
        );

        return $response;
    }
}