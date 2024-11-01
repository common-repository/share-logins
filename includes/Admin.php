<?php
/**
 * All admin facing functions
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
 * @subpackage Admin
 * @author Nazmul Ahsan <n.mukto@gmail.com>
 */
class Admin extends Hooks {

    /**
     * Constructor function
     */
    public function __construct( $plugin ) {
        $this->slug     = $plugin['TextDomain'];
        $this->name     = $plugin['Name'];
        $this->version  = $plugin['Version'];
    }

    /**
     * Add some script to head
     */
    public function head() {

    }

    public function install() {

        /**
         * Create database table to store activity logs
         */
        global $wpdb;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $log_table = "{$wpdb->prefix}share_logins_log";
        $pu_sql = "CREATE TABLE $log_table (
            id int(11) NOT NULL AUTO_INCREMENT,
            time int(10) NOT NULL,
            activity varchar(15) NOT NULL,
            direction varchar(10) NOT NULL,
            url varchar(225) NOT NULL,
            user varchar(225) NOT NULL,
            UNIQUE KEY id (id)
        );";

        dbDelta( $pu_sql );

        /**
         * Schedule an event to sync help docs
         */
        if ( !wp_next_scheduled ( 'cx_daily_event' )) {
            wp_schedule_event( time(), 'daily', 'cx_daily_event' );
        }
    }

    /**
     * @since 2.0
     */
    public function mu_install( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
        
        global $wpdb;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $log_table = "{$wpdb->base_prefix}{$blog_id}_share_logins_log";
        $pu_sql = "CREATE TABLE $log_table (
            id int(11) NOT NULL AUTO_INCREMENT,
            time int(10) NOT NULL,
            activity varchar(15) NOT NULL,
            direction varchar(10) NOT NULL,
            url varchar(225) NOT NULL,
            user varchar(225) NOT NULL,
            UNIQUE KEY id (id)
        );";

        dbDelta( $pu_sql );

        /**
         * Schedule an event to sync help docs
         */
        if ( !wp_next_scheduled ( 'cx_daily_event' )) {
            wp_schedule_event( time(), 'daily', 'cx_daily_event' );
        }
    }

    public function uninstall() {
        wp_clear_scheduled_hook( 'cx_daily_event' );
    }

    /**
     * @since 2.0
     */
    public function mu_uninstall( $blog_id, $drop ) {
        wp_clear_scheduled_hook( 'cx_daily_event' );
    }

    public function add_schedules( $schedules ) {

        $schedules['weekly'] = array(
            'interval'  => WEEK_IN_SECONDS,
            'display'   => __( 'Weekly', 'share-logins' )
        );

        $schedules['fortnightly'] = array(
            'interval'  => WEEK_IN_SECONDS * 2,
            'display'   => __( 'Fortnightly', 'share-logins' )
        );

        $schedules['monthly'] = array(
            'interval'  => MONTH_IN_SECONDS,
            'display'   => __( 'Monthly', 'share-logins' )
        );
        
        return $schedules;
    }
    
    /**
     * Enqueue JavaScripts and stylesheets
     */
    public function enqueue_scripts( $hook ) {

        if ( $hook == 'toplevel_page_share-logins' || $hook == 'share-logins_page_share-logins-logs' ) {

            $min = defined( 'CXSL_DEBUG' ) && CXSL_DEBUG ? '' : '.min';
            
            wp_enqueue_style( $this->name .'-select2.min', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css', '', '4.0.5', 'all' );
            wp_enqueue_style( $this->slug .'-upgrade-notice', plugins_url( "/assets/css/upgrade-notice{$min}.css", CXSL ), '', $this->version, 'all' );
            wp_enqueue_style( $this->slug, plugins_url( "/assets/css/admin{$min}.css", CXSL ), '', $this->version, 'all' );
            wp_enqueue_style( $this->slug .'-responsive', plugins_url( "/assets/css/responsive{$min}.css", CXSL ), '', $this->version, 'all' );

            wp_enqueue_script( $this->name . '-select2.min', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js', array( 'jquery' ), '4.0.5', true );
            wp_enqueue_script( $this->slug, plugins_url( "/assets/js/admin{$min}.js", CXSL ), array( 'jquery' ), $this->version, true );
        }
    }

    /**
     * Sync docs from https://help.codexpert.io daily
     */
    public function sync_docs() {
        $json_url = 'https://help.codexpert.io/wp-json/wp/v2/docs/?parent=165&per_page=20';
        if( !is_wp_error( $data = wp_remote_get( $json_url ) ) ) {
            update_option( 'share-logins-docs-json', json_decode( $data['body'], true ) );
        }
    }

    public function overlay() {
        echo '
        <div id="cx-validate-wrap" style="display: none;">
            <div id="cx-validate-report">
                <span class="cx-report-close">&times;</span>
                <div id="cx-report-view"></div>
            </div>
        </div>
        ';
    }

    public function admin_bar_menu( $wp_admin_bar ) {
        if( is_admin() || !current_user_can( 'manage_options' ) ) return;
        
        $menu = array(
            'id'    => $this->name,
            'title' => __( 'Share Logins', 'share-logins' ),
            'href'  => admin_url( "admin.php?page=share-logins" ),
            'meta'  => array( 'class' => 'share-logins' )
        );
        $wp_admin_bar->add_node( $menu );

        /**
         * @since 3.0.0
         */
        if( cx_log_enabled() ) :
        $submenu = array(
            'id'    => "{$this->name}-logs",
            'title' => __( 'Activity Logs', 'share-logins' ),
            'href'  => admin_url( "admin.php?page=share-logins-logs" ),
            'meta'  => array( 'class' => 'share-logins' ),
            'parent'=> $this->name
        );
        $wp_admin_bar->add_node( $submenu );
        endif;
    }
}