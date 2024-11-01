<?php
if( ! function_exists( 'pri' ) ) :
function pri( $data ) {
    echo '<pre>';
    if( is_object( $data ) || is_array( $data ) ) {
        print_r( $data );
    }
    else {
        var_dump( $data );
    }
    echo '</pre>';
}
endif;

if( ! function_exists( 'cx_get_posts' ) ) :
function cx_get_posts( $post_type = 'post', $limit = -1 ) {
    $arg = array(
        'post_type'         => $post_type,
        'posts_per_page'    => $limit
        );
    $p = new WP_Query( $arg );

    $posts = array( '' => __( '- Choose a post -', 'share-logins' ) );

    foreach( $p->posts as $post ) :
        $posts[ $post->ID ] = $post->post_title;
    endforeach;

    return apply_filters( 'cx_get_posts', $posts, $post_type, $limit );
}
endif;

if( ! function_exists( 'cx_get_option' ) ) :
function cx_get_option( $key, $section, $default = '' ) {

    $options = get_option( $key );

    if ( isset( $options[ $section ] ) ) {
        return $options[ $section ];
    }

    return $default;
}
endif;

if( !function_exists( 'cx_get_template' ) ) :
/**
 * Includes a template file resides in /templates diretory
 *
 * It'll look into /share-logins directory of your active theme
 * first. if not found, default template will be used.
 * can be overriden with share-logins_template_override_dir hook
 *
 * @param string $slug slug of template. Ex: template-slug.php
 * @param string $sub_dir sub-directory under base directory
 * @param array $fields fields of the form
 */
function cx_get_template( $slug, $sub_dir = null, $fields = null ) {

    // templates can be placed in this directory
    $override_template_dir = apply_filters( 'share-logins_template_override_dir', get_stylesheet_directory() . '/share-logins/', $slug, $sub_dir, $fields );
    // if it's under a sub-directory
    $override_template_dir .= ( ! is_null( $sub_dir ) ) ? trailingslashit( $sub_dir ) : '';

    // default template directory
    $plugin_template_dir = dirname( CXSL ) . '/templates/';
    // if it's under a sub-directory
    $plugin_template_dir .= ( ! is_null( $sub_dir ) ) ? trailingslashit( $sub_dir ) : '';

    // full path of a template file in plugin directory
    $plugin_template_path =  $plugin_template_dir . $slug . '.php';
    // full path of a template file in override directory
    $override_template_path =  $override_template_dir . $slug . '.php';

    // if template is found in override directory
    if( file_exists( $override_template_path ) ) {
        ob_start();
        include_once $override_template_path;
        return ob_get_clean();
    }
    // otherwise use default one
    elseif ( file_exists( $plugin_template_path ) ) {
        ob_start();
        include_once $plugin_template_path;
        return ob_get_clean();
    }
    else {
        return __( 'Template not found!', 'share-logins' );
    }

}
endif;

if( ! function_exists( 'ncrypt' ) ) :
function ncrypt() {
    $ncrypt = new \mukto90\Ncrypt;

    $options = get_option( 'share-logins_security', array() );
    $secret_key = isset( $options['secret_key'] ) ? $options['secret_key'] : 'rd4jd874hey64t';
    $secret_iv  = isset( $options['secret_iv'] ) ? $options['secret_iv'] : '8su309fr7uj34';
    $ncrypt->set_secret_key( $secret_key );
    $ncrypt->set_secret_iv( $secret_iv );
    $ncrypt->set_cipher( 'AES-256-CBC' );

    return $ncrypt;
}
endif;

if( ! function_exists( 'cx_auto_login' ) ) :
function cx_auto_login( $username, $remember = 1 ) {
    if( is_user_logged_in() ) return;
    
    $user = get_user_by( 'login', $username );
    $user_id = $user->ID;

    wp_set_current_user( $user_id, $username );
    wp_set_auth_cookie( $user_id, ( $remember == 1 ) );
    do_action( 'wp_login', $username, $user );
}
endif;

if( ! function_exists( 'cx_get_remote_sites' ) ) :
function cx_get_remote_sites() {
    $remote_sites = array();
    $sites = get_option( 'share-logins_remote_sites' ) ? : array();
    foreach ( $sites as $site ) {
        if( $site != '' && trailingslashit( $site ) != trailingslashit( get_bloginfo( 'url' ) ) ) {
            $remote_sites[] = untrailingslashit( $site );
        }
    }

    if( !function_exists( 'cx_is_active' ) && count( $remote_sites ) > 0 ) return array( $remote_sites[0] );

    return $remote_sites;
}
endif;

if( ! function_exists( 'cx_get_access_token' ) ) :
function cx_get_access_token() {
    $access_token = cx_get_option( 'share-logins_security', 'access_token' );
    if( $access_token == '' ) $access_token = 'gTEt35Ugy2igtyu8H99oOherhRJUR684H78yy';

    return $access_token;
}
endif;

if( ! function_exists( 'cx_get_secret_key' ) ) :
function cx_get_secret_key() {
    $secret_key = cx_get_option( 'share-logins_security', 'secret_key' );
    if( $secret_key == '' ) $secret_key = 'rd4jd874hey64t';

    return $secret_key;
}
endif;

if( ! function_exists( 'cx_get_secret_iv' ) ) :
function cx_get_secret_iv() {
    $secret_iv = cx_get_option( 'share-logins_security', 'secret_iv' );
    if( $secret_iv == '' ) $secret_iv = '8su309fr7uj34';

    return $secret_iv;
}
endif;

if( ! function_exists( 'cx_within_route' ) ) :
function cx_within_route() {
    return strpos( $_SERVER['REQUEST_URI'], CXSL_API_NAMESPACE ) !== false;
}
endif;

if( ! function_exists( 'cx_config_is_enabled' ) ) :
/**
 * @var $type string possible values outgoing or incoming
 */
function cx_config_is_enabled( $type, $url, $action ) {
    $_config = get_option( "share-logins_config_{$type}" );
    return isset( $_config[ $url ][ $action ] ) && $_config[ $url ][ $action ] == 'on';
}
endif;

if( ! function_exists( 'cx_set_scheduled_urls' ) ) :
function cx_set_scheduled_urls( $urls ) {
    $_SESSION['_share-logins_scheduled_urls'] = base64_encode( serialize( $urls ) );

    /**
     * @since 2.1.3
     * @author developerwil
     * @link https://wordpress.org/support/topic/sessions-need-to-be-destroyed/
     */
    session_write_close();
}
endif;

if( ! function_exists( 'cx_get_scheduled_urls' ) ) :
function cx_get_scheduled_urls() {
    if( !isset( $_SESSION['_share-logins_scheduled_urls'] ) ) return array();
    $urls = unserialize( base64_decode( $_SESSION['_share-logins_scheduled_urls'] ) );
    return $urls;
}
endif;

if( ! function_exists( 'cx_remove_scheduled_url' ) ) :
function cx_remove_scheduled_url( $url ) {
    $urls = cx_get_scheduled_urls();
    
    if( isset( $urls[ $url ] ) ) {
        unset( $urls[ $url ] );
    }

    cx_set_scheduled_urls( $urls );
}
endif;

if( ! function_exists( 'cx_clean_scheduled_urls' ) ) :
function cx_clean_scheduled_urls() {
    if( !isset( $_SESSION['_share-logins_scheduled_urls'] ) ) return;
    
    unset( $_SESSION['_share-logins_scheduled_urls'] );
}
endif;

if( ! function_exists( 'cx_log_enabled' ) ) :
function cx_log_enabled() {
    return cx_get_option( 'share-logins_basics', 'enable_log' ) == 'on';
}
endif;

if( ! function_exists( 'cx_add_log' ) ) :
function cx_add_log( $activity, $direction, $user, $url ) {

    if( !cx_log_enabled() ) return;

    global $wpdb;

    $log_table = "{$wpdb->prefix}share_logins_log";

    if( is_multisite() ) {
        $blog_id = get_current_blog_id();
        $log_table = "{$wpdb->base_prefix}{$blog_id}_share_logins_log";
    }

    $wpdb->insert(
        $log_table,
        array(
            'time'          => time(),
            'activity'      => $activity,
            'direction'     => $direction,
            'url'           => $url,
            'user'          => $user
        ),
        array(
            '%d',
            '%s',
            '%s',
            '%s',
            '%s'
        )
    );
}
endif;

if( ! function_exists( 'cx_get_route_home' ) ) :
function cx_get_route_home( $url ) {
    $url_parts = explode( '/?', $url );
    return $url_parts[0];
}
endif;

if( ! function_exists( 'cx_is_pro' ) ) :
function cx_is_pro() {
    return apply_filters( 'cx_is_pro', false );
}
endif;

function cx_user_meta_keys() {
    $user_meta_keys = wp_cache_get( 'user_meta_keys', 'share-logins' );

    if( false === $user_meta_keys ) {

        global $wpdb;
        $sql = "SELECT distinct meta_key FROM {$wpdb->usermeta}";
        $result = $wpdb->get_results( $sql );

        $meta_keys = array();
        foreach ( $result as $row ) {
            $meta_keys[ $row->meta_key ] = $row->meta_key;
        }
        
        wp_cache_set( 'user_meta_keys', $meta_keys, 'share-logins', DAY_IN_SECONDS );

        return $meta_keys;
    }
    
    return $user_meta_keys;

}

function cx_get_user_roles() {
    global $wp_roles;
    $roles = $wp_roles->get_names();

    $all_roles = array();
    foreach ( $roles as $key => $value ) {
        $all_roles[ $key ] = $value;
    }

    return $all_roles;
}

function cx_validation_report( $remote_site ) {
    $access_token   = cx_get_access_token();
    $secret_key     = cx_get_secret_key();
    $secret_iv      = cx_get_secret_iv();
    $home_url       = untrailingslashit( get_bloginfo( 'url' ) );
    $remote_site    = untrailingslashit( $remote_site );

    $url = "{$remote_site}/?rest_route=/" . CXSL_API_NAMESPACE . "/validate";
    $data = wp_remote_get( add_query_arg( array( 'site_url' => $home_url, 'access_token' => $access_token, 'secret_key' => $secret_key, 'secret_iv' => $secret_iv ), $url ) );

    $report = array();
    if( !is_wp_error( $data ) && $data['response']['code'] == 200 ) {
        $report = json_decode( $data['body'], true );
    }
    
    $report['local'] = array(
        'license'   => cx_is_pro() && function_exists( 'cx_is_active' ) && cx_is_active(),
        'incoming'  => array(
            'login'     => cx_config_is_enabled( 'incoming', $remote_site, 'login' ),
            'logout'    => cx_config_is_enabled( 'incoming', $remote_site, 'logout' ),
            'create'    => cx_config_is_enabled( 'incoming', $remote_site, 'create-user' ),
            'update'    => cx_config_is_enabled( 'incoming', $remote_site, 'update-user' ),
            'delete'    => cx_config_is_enabled( 'incoming', $remote_site, 'delete-user' ),
            'reset'     => cx_config_is_enabled( 'incoming', $remote_site, 'reset-password' )
        ),
        'outgoing'  => array(
            'login'     => cx_config_is_enabled( 'outgoing', $remote_site, 'login' ),
            'logout'    => cx_config_is_enabled( 'outgoing', $remote_site, 'logout' ),
            'create'    => cx_config_is_enabled( 'outgoing', $remote_site, 'create-user' ),
            'update'    => cx_config_is_enabled( 'outgoing', $remote_site, 'update-user' ),
            'delete'    => cx_config_is_enabled( 'outgoing', $remote_site, 'delete-user' ),
            'reset'     => cx_config_is_enabled( 'outgoing', $remote_site, 'reset-password' )
        )
    );

    return $report;
}

function cx_validate_message( $report, $site_type, $direction, $action ) {
    if( !cx_is_pro() && !in_array( $action, array( 'login', 'logout' ) ) ) return __( 'Pro Feature', 'share-logins' );

    return isset( $report[ $site_type ][ $direction ][ $action ] ) && $report[ $site_type ][ $direction ][ $action ] == 1 ? __( '<span class="dashicons dashicons-yes-alt cx-green"></span>', 'share-logins' ) : __( '<span class="dashicons dashicons-dismiss cx-red"></span>', 'share-logins' );
}

function cx_validate_icon( $report, $site_type, $action ) {
    return ( $report[ $site_type ][ 'incoming' ][ $action ] == 1 && $report[ $site_type ][ 'outgoing' ][ $action ] == 1 ) ? __( 'Y', 'share-logins' ) : __( 'N', 'share-logins' );
}

if( ! function_exists( 'cx_is_role_allowed' ) ) :
/**
 * Defines if the user's role is allowed for sync
 */
function cx_is_role_allowed( $user ) {
    $roles = cx_get_option( 'share-logins_basics', 'user_roles', array() );

    if( !is_array( $roles ) || count( $roles ) <= 0 ) return true;
    
    return count( array_intersect( $roles, $user->roles ) ) > 0;
}
endif;

if( ! function_exists( 'cx_validation_array_formation' ) ) :
/**
 * Defines if the user's role is allowed for sync
 */
function cx_validation_array_formation( $array = array(), $site_added = 1 ) {

    $new_array = array();

    foreach ( $array as $key => $value ) {
        $value = $value == 'on' && $site_added == 1 ? 1 : 0;

        if( strpos( $key, '-') ){
            $new_key = explode( '-', $key );
            $new_array[$new_key[0]] = $value;
        }else{
            $new_array[$key] = $value;
        }
    }

    return $new_array;
}
endif;