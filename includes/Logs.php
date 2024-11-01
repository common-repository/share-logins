<?php
/**
 * All activity logs related functions
 */
namespace codexpert\Share_Logins;

// WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Create a new table class that will extend the WP_List_Table
 */
class Logs extends \WP_List_Table {
    
    /**
     * Constructor function
     */
    public function __construct( $name, $version ) {
        parent::__construct();
        $this->name     = $name;
        $this->version  = $version;
    }

    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items() {
        $columns    = $this->get_columns();
        $hidden     = $this->get_hidden_columns();
        $sortable   = $this->get_sortable_columns();

        $data = $this->table_data();

        $timestamp = array();
        foreach ( $data as $value ) {
        	$timestamp[] = strtotime( $value['time'] );
        }
        
        usort( $timestamp, array( &$this, 'sort_data' ) );

        $per_page       = 50;
        $current_page   = $this->get_pagenum();
        $total_items    = count( $data );

        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page
        ) );

        $data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

        $this->_column_headers = array( $columns, $hidden, $sortable );
        $this->items = $data;
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns() {
        $columns = array(
            'time'      => __( 'Time', 'share-logins' ),
            'activity'  => __( 'Activity', 'share-logins' ),
            'direction' => __( 'Direction', 'share-logins' ),
            'url'       => __( 'URL', 'share-logins' ),
            'user'      => __( 'Username', 'share-logins' ),
        );

        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns() {
        return array();
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns() {
        return array(
            'time'      => array( 'time', false ),
            'activity'  => array( 'activity', false ),
            'direction' => array( 'direction', false ),
            'url'       => array( 'url', false ),
            'user'      => array( 'user', false ),
        );
    }

    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data() {
        $data = array();
        global $wpdb;
        
        $log_table = "{$wpdb->prefix}share_logins_log";

        if( is_multisite() ) {
            $blog_id = get_current_blog_id();
            $log_table = "{$wpdb->base_prefix}{$blog_id}_share_logins_log";
        }
        
        $sql = "SELECT * FROM $log_table GROUP BY(time)";
        if( isset( $_REQUEST['s'] ) ) {
            $s = $_REQUEST['s'];
            $sql .= " WHERE activity LIKE '%{$s}%' OR direction LIKE '%{$s}%' OR url LIKE '%{$s}%' OR user LIKE '%{$s}%'";
        }
        $sql .= " ORDER BY `time` DESC";

        $logs = $wpdb->get_results( $sql );

        $format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
        foreach ( $logs as $log ) {
            $data[] = array(
                'time'      => date( $format, $log->time ),
                'activity'  => $log->activity,
                'direction' => $log->direction,
                'url'       => $log->url,
                'user'      => $log->user,
            );
        }
        return $data;
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name ) {
        return $item[ $column_name ];
    }

    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data( $a, $b ) {
        // Set defaults
        $orderby = 'time';
        $order = 'desc';

        // If orderby is set, use this as the sort column
        if( ! empty( $_GET['orderby'] ) ) {
            $orderby = $_GET['orderby'];
        }

        // If order is set use this as the order
        if( ! empty( $_GET['order'] ) ) {
            $order = $_GET['order'];
        }

        $result = strcmp( $a[ $orderby ], $b[ $orderby ] );

        if( $order === 'asc' ) {
            return $result;
        }

        return -$result;
    }
}