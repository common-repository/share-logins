<?php
/**
 * All AJAX facing functions
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
 * @subpackage AJAX
 * @author Nazmul Ahsan <n.mukto@gmail.com>
 */
class AJAX extends Hooks {

    /**
     * Constructor function
     */
    public function __construct( $plugin ) {
        $this->slug     = $plugin['TextDomain'];
        $this->name     = $plugin['Name'];
        $this->version  = $plugin['Version'];
    }

    public function validate() {
    	extract( $_POST );
    	$html = cx_get_template( 'validate', 'settings', array( 'remote_site' => $remote_site ) );
    	wp_die( $html );
    }

}