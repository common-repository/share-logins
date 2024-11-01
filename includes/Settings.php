<?php
/**
 * All settings facing functions
 */

namespace codexpert\Share_Logins;
use codexpert\Product\License;

/**
 * @package Plugin
 * @subpackage Settings
 * @author Nazmul Ahsan <n.mukto@gmail.com>
 */
class Settings extends Hooks {

    /**
     * Constructor function
     */
    public function __construct( $plugin ) {
        $this->slug     = $plugin['TextDomain'];
        $this->name     = $plugin['Name'];
        $this->version  = $plugin['Version'];
    }
    
    public function init() {
        
        $settings = array(
            'id'            => $this->slug,
            'label'         => $this->name,
            'title'         => $this->name,
            'header'        => $this->name,
            'priority'      => 60,
            'capability'    => 'manage_options',
            'icon'          => 'dashicons-share', // dashicon or a URL to an image
            'position'      => 25,
            'sections'      => array(
                'share-logins_remote_sites' => array(
                    'id'        => 'share-logins_remote_sites',
                    'label'     => __( 'Remote Sites', 'share-logins' ),
                    'desc'      => __( 'Remote sites are your other sites that you want to sync with the current site. List your remote sites here. Please make sure you have added their <strong>homepage URLs</strong> only.<br/>After adding a remote site, don\'t forget to configure outgoing and incoming requests from the left menu!', 'share-logins' ),
                    'icon'      => 'dashicons-networking',
                    'fields'    => array(),
                    'color'     => '#d52054',
                    'sticky'    => true,
                ),
                'share-logins_config_outgoing' => array(
                    'id'        => 'share-logins_config_outgoing',
                    'label'     => __( 'Outgoing Requests', 'share-logins' ),
                    'desc'      => __( 'Choose which of the activities you want to sync <strong>from this site to your remote sites</strong>. For example, if you uncheck <strong>\'Create User\'</strong> for a specific site, creating a new user here won\'t create it in that site.', 'share-logins' ),
                    'icon'      => 'dashicons-arrow-right-alt',
                    'fields'    => array(),
                    'color'     => '#1272d6',
                    'sticky'    => true,
                ),
                'share-logins_config_incoming' => array(
                    'id'        => 'share-logins_config_incoming',
                    'label'     => __( 'Incoming Requests', 'share-logins' ),
                    'desc'      => __( 'Choose which of the activities you want to sync <strong>from your remote sites to this site</strong>. For example, if you uncheck <strong>\'Login\'</strong> for a specific site, when you log in to that site, it won\'t log you in to this site.', 'share-logins' ),
                    'icon'      => 'dashicons-arrow-left-alt',
                    'fields'    => array(),
                    'color'     => '#ee6602',
                    'sticky'    => true,
                ),
                'share-logins_validation' => array(
                    'id'        => 'share-logins_validation',
                    'label'     => __( 'Validation', 'share-logins' ),
                    'desc'      => __( 'You can validate if your remote sites are successfully connected and properly configured. Click the <strong>Validate</strong> button next to the site URL to see the report.', 'share-logins' ),
                    'icon'      => 'dashicons-yes',
                    'hide_form' => true,
                    'fields'    => array(),
                    'color'     => '#6a27b9',
                ),
                'share-logins_basics' => array(
                    'id'        => 'share-logins_basics',
                    'label'     => __( 'Basic Settings', 'share-logins' ),
                    'icon'      => 'dashicons-admin-tools',
                    'page_load' => true,
                    'sticky'    => true,
                    'color'     => '#00a655',
                    'fields'    => array(
                        'enable_log' => array(
                            'id'              => 'enable_log',
                            'type'            => 'checkbox',
                            'label'           => __( 'Enable Log', 'share-logins' ),
                            'desc'            => __( 'Enable log for every activity', 'share-logins' ),
                        ),
                        'user_roles' => array(
                            'id'        => 'user_roles',
                            'type'      => 'select',
                            'label'     => __( 'Allowed User Roles', 'share-logins' ),
                            'desc'      => __( 'Choose user roles that should be synced activities for. Multiple can be selected. All user roles would sync if it\'s left empty!', 'share-logins' ),
                            'multiple'  => true,
                            'chosen'    => true,
                            'options'   => cx_get_user_roles(),
                        )
                    )
                ),
                'share-logins_security' => array(
                    'id'        => 'share-logins_security',
                    'label'     => __( 'Security Settings', 'share-logins' ),
                    'desc'      => __( 'Configure your security settings. You can keep these keys as they are, but it\'s <strong>STRONGLY recommended</strong> that you change them. Just use some random characters that nobody can guess! Also, plaese make sure that you have <strong>EXACTLY the same keys</strong> configured across all your Remote Sites.', 'share-logins' ),
                    'icon'      => 'dashicons-shield',
                    'sticky'    => true,
                    'color'     => '#b552e3',
                    'fields'    => array(
                        'access_token' => array(
                            'id'              => 'access_token',
                            'type'              => 'text',
                            'label'             => __( 'Access Token', 'share-logins' ),
                            'desc'              => __( 'Alphanumeric only.', 'share-logins' ),
                            'placeholder'       => 'gTEt35Ugy2igtyu8H99oOherhRJUR684H78yy',
                            'default'           => 'gTEt35Ugy2igtyu8H99oOherhRJUR684H78yy'
                        ),
                        'secret_key' => array(
                            'id'              => 'secret_key',
                            'type'              => 'text',
                            'label'             => __( 'Secret Key', 'share-logins' ),
                            'desc'              => __( 'Alphanumeric only.', 'share-logins' ),
                            'placeholder'       => 'rd4jd874hey64t',
                            'default'           => 'rd4jd874hey64t'
                        ),
                        'secret_iv' => array(
                            'id'              => 'secret_iv',
                            'type'              => 'text',
                            'label'             => __( 'Secret IV', 'share-logins' ),
                            'desc'              => __( 'Alphanumeric only.', 'share-logins' ),
                            'placeholder'       => '8su309fr7uj34',
                            'default'           => '8su309fr7uj34'
                        )
                    )
                ),
                'share-logins_migrate_users' => array(
                    'id'        => 'share-logins_migrate_users',
                    'label'     => __( 'Migrate Users', 'share-logins' ),
                    'icon'      => 'dashicons-groups',
                    'hide_form' => true,
                    'color'     => '#cb3340',
                    'fields'    => array()
                ),
                'share-logins_help' => array(
                    'id'        => 'share-logins_help',
                    'label'     => __( 'Help', 'share-logins' ),
                    'icon'      => 'dashicons-sos',
                    'hide_form' => true,
                    'color'     => '#009fff',
                    'fields'    => array()
                ),
                'share-logins_upgrade' => array(
                    'id'        => 'share-logins_upgrade',
                    'label'     => __( 'Upgrade', 'share-logins' ),
                    'icon'      => 'dashicons-unlock',
                    'hide_form' => true,
                    'color'     => '#572e8d',
                    'fields'    => array()
                ),
            ),
        );

        new \CX_Settings_API( $settings );

        /**
         * @since 3.0.0
         */
        if( cx_log_enabled() ) :

        $log = array(
            'id'            => "{$this->slug}-logs",
            'label'         => __( 'Activity Logs', 'share-logins' ),
            'title'         => $this->name,
            'header'        => $this->name,
            'priority'      => 60,
            'parent'        => $this->slug,
            'capability'    => 'manage_options',
            'icon'          => 'dashicons-share',
            'sections'      => array(
                'share-logins_activity_logs' => array(
                    'id'        => 'share-logins_activity_logs',
                    'label'     => __( 'Activity Logs', 'share-logins' ),
                    'icon'      => 'dashicons-media-spreadsheet',
                    'hide_form' => true,
                    'fields'    => array()
                ),
            )
        );

        new \CX_Settings_API( $log );
        endif;
    }

    public function insert_fields( $section ) {
        if( $section['id'] == 'share-logins_remote_sites' ) echo cx_get_template( 'remote-sites', 'settings' );
        elseif( $section['id'] == 'share-logins_config_outgoing' ) echo cx_get_template( 'config-outgoing', 'settings' );
        elseif( $section['id'] == 'share-logins_config_incoming' ) echo cx_get_template( 'config-incoming', 'settings' );
        else return;
    }

    public function section_content( $section ) {
        if( $section['id'] == 'share-logins_activity_logs' ) echo cx_get_template( 'logs', 'settings', array( 'name' => $this->name, 'version' => $this->version ) );
        elseif( $section['id'] == 'share-logins_validation' ) echo cx_get_template( 'validation', 'settings' );
        elseif( $section['id'] == 'share-logins_migrate_users' ) echo cx_get_template( 'migrate', 'settings' );
        elseif( $section['id'] == 'share-logins_upgrade' ) echo cx_get_template( 'upgrade-notice', 'settings' );
        elseif( $section['id'] == 'share-logins_help' ) echo cx_get_template( 'help', 'settings' );
        return;
    }

    public function skip_save( $is_savable, $section, $posted ) {

        switch ( $section ) {
            case 'share-logins_remote_sites':
                $sites = array();
                if( isset( $posted['share-logins_remote_sites'] ) && is_array( $posted['share-logins_remote_sites'] ) ) :
                foreach ( $posted['share-logins_remote_sites'] as $site ) {
                    $sites[] = $site;
                }
                endif;

                update_option( 'share-logins_remote_sites', $sites );

                add_filter( 'cx-settings-response', function( $response, $posted ) {
                    if( $posted['option_name'] != 'share-logins_remote_sites' ) return $response;

                    return array( 'status' => 1, 'message' => __( 'Settings Saved!' ), 'page_load' => 1 );
                }, 10, 4 );
                break;
                
            case 'share-logins_config_outgoing':
                $sites = array();
                if( isset( $posted['share-logins_config_outgoing'] ) && is_array( $posted['share-logins_config_outgoing'] ) ) :
                foreach ( $posted['share-logins_config_outgoing'] as $site => $options ) {
                    $sites[ $site ] = $options;
                }
                endif;

                update_option( 'share-logins_config_outgoing', $sites );

                add_filter( 'cx-settings-response', function( $response, $posted ) {
                    if( $posted['option_name'] != 'share-logins_config_outgoing' ) return $response;

                    return array( 'status' => 1, 'message' => __( 'Settings Saved!' ), 'page_load' => 0 );
                }, 10, 4 );
                break;
                
            case 'share-logins_config_incoming':
                $sites = array();
                if( isset( $posted['share-logins_config_incoming'] ) && is_array( $posted['share-logins_config_incoming'] ) ) :
                foreach ( $posted['share-logins_config_incoming'] as $site => $options ) {
                    $sites[ $site ] = $options;
                }
                endif;

                update_option( 'share-logins_config_incoming', $sites );

                add_filter( 'cx-settings-response', function( $response, $posted ) {
                    if( $posted['option_name'] != 'share-logins_config_incoming' ) return $response;

                    return array( 'status' => 1, 'message' => __( 'Settings Saved!' ), 'page_load' => 0 );
                }, 10, 4 );
                break;
            
            default:
                return $is_savable;
                break;
        }
    }

    public function action_settings_link( $links ) {
        $links[] = sprintf( __( '<a href="%s">Settings</a>', 'share-logins' ), admin_url( 'admin.php?page=' . $this->slug ) );
        return $links;
    }
}