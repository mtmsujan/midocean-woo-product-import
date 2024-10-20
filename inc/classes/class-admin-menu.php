<?php

namespace BULK_IMPORT\Inc;

defined( "ABSPATH" ) || exit( "Direct Access Not Allowed" );

use BULK_IMPORT\Inc\Traits\Singleton;
use BULK_IMPORT\Inc\Traits\Program_Logs;

class Admin_Menu {

    use Singleton;
    use Program_Logs;

    public function __construct() {
        $this->setup_hooks();
    }

    public function setup_hooks() {
        add_action( 'admin_menu', [ $this, 'register_admin_menu' ] );
        add_action( 'admin_menu', [ $this, 'register_csv_import_menu' ] );
        add_action( 'admin_menu', [ $this, 'register_sheet_import_menu' ] );
        add_action( 'wp_ajax_save_client_credentials', [ $this, 'save_client_credentials' ] );
        add_action( 'wp_ajax_save_table_prefix', [ $this, 'save_table_prefix' ] );
        add_action( 'wp_ajax_save_profit_percentage', [ $this, 'save_profit_percentage_callback' ] );
    }

    public function register_admin_menu() {
        add_menu_page(
            __( 'Bulk Product Import', 'bulk-product-import' ),
            __( 'Bulk Product Import', 'bulk-product-import' ),
            'manage_options',
            'bulk_product_import',
            [ $this, 'bulk_product_import_page_html' ],
            'dashicons-cloud-upload',
            80
        );
    }

    public function register_csv_import_menu() {
        add_submenu_page(
            'bulk_product_import',
            'CSV Import',
            'CSV Import',
            'manage_options',
            'bulk_product_csv_import',
            [ $this, 'bulk_product_csv_import_page_html' ]
        );
    }

    public function register_sheet_import_menu() {
        add_submenu_page(
            'bulk_product_import',
            'Sheet Import',
            'Sheet Import',
            'manage_options',
            'bulk_product_sheet_import',
            [ $this, 'bulk_product_sheet_import_page_html' ]
        );
    }

    public function bulk_product_import_page_html() {
        ?>

        <div class="entry-header">
            <h1 class="entry-title text-center mt-3" style="color: #2271B1">
                <?php esc_html_e( 'WooCommerce Bulk Product Import', 'bulk-product-import' ); ?>
            </h1>
        </div>

        <div id="be-tabs" class="mt-3">
            <div id="tabs">

                <ul class="nav nav-pills">
                    <li class="nav-item"><a href="#api"
                            class="nav-link be-nav-links"><?php esc_html_e( 'API', 'bulk-product-import' ); ?></a></li>
                    <li class="nav-item"><a href="#tables"
                            class="nav-link be-nav-links"><?php esc_html_e( 'Tables', 'bulk-product-import' ); ?></a></li>
                    <li class="nav-item"><a href="#endpoints"
                            class="nav-link be-nav-links"><?php esc_html_e( 'Endpoints', 'bulk-product-import' ); ?></a></li>
                    <li class="nav-item"><a href="#profit"
                            class="nav-link be-nav-links"><?php esc_html_e( 'Profit Percentage', 'bulk-product-import' ); ?></a>
                    </li>
                </ul>

                <div id="api">
                    <?php include BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/inc/template-parts/template-api.php'; ?>
                </div>

                <div id="tables">
                    <?php include BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/inc/template-parts/template-tables.php'; ?>
                </div>

                <div id="endpoints">
                    <div id="api-endpoints" class="common-shadow">
                        <h4>
                            <?php _e( 'API Endpoints', 'bulk-product-import' ); ?>
                        </h4>

                        <div id="api-endpoints-table">
                            <?php include BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/inc/template-parts/template-endpoints.php'; ?>
                        </div>
                    </div>
                </div>

                <div id="profit">
                    <?php $profit_percentage = get_option( 'be-profit-percentage' ); ?>
                    <div id="profit-percentage" class="common-shadow">
                        <h4>
                            <?php _e( 'Set Profit Percentage', 'bulk-product-import' ); ?>
                        </h4>

                        <div class="profit-percentage-body d-flex align-items-center gap-2 mt-3">
                            <h6><?php _e( 'Profit Percentage (%):', 'bulk-product-import' ); ?></h6>
                            <input type="number" name="profit-percentage-input-field" id="profit-percentage-input-field"
                                value="<?php echo $profit_percentage; ?>">
                        </div>

                        <button type="button" class="btn btn-primary mt-3" id="profit-percentage-save-button">Save</button>
                    </div>
                </div>

            </div>
        </div>

        <?php
    }

    public function bulk_product_csv_import_page_html() {
        ?>

        <div class="entry-header">
            <h1 class="entry-title text-center mt-3" style="color: #2271B1">
                <?php esc_html_e( 'WooCommerce Bulk Product Import CSV', 'bulk-product-import' ); ?>
            </h1>
        </div>

        <div class="wrap">
            <?php include BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/inc/template-parts/template-csv.php'; ?>
        </div>

        <?php
    }

    public function bulk_product_sheet_import_page_html() {
        ?>

        <div class="entry-header">
            <h1 class="entry-title text-center mt-3" style="color: #2271B1">
                <?php esc_html_e( 'WooCommerce Bulk Product Import Sheet', 'bulk-product-import' ); ?>
            </h1>
        </div>

        <div class="wrap">
            <?php include BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/inc/template-parts/template-sheet.php'; ?>
        </div>

        <?php
    }

    public function save_client_credentials() {
        check_ajax_referer( 'bulk_product_import_nonce', 'nonce' );

        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Unauthorized user', 'bulk-product-import' ) );
        }

        $client_id     = sanitize_text_field( $_POST['client_id'] );
        $client_secret = sanitize_text_field( $_POST['client_secret'] );
        $api_key       = sanitize_text_field( $_POST['api_key'] );

        update_option( 'be-client-id', $client_id );
        update_option( 'be-client-secret', $client_secret );
        update_option( 'be-api-key', $api_key );

        wp_send_json_success( __( 'Credentials saved successfully', 'bulk-product-import' ) );
    }

    public function save_table_prefix() {

        check_ajax_referer( 'bulk_product_import_nonce', 'nonce' );

        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Unauthorized user', 'bulk-product-import' ) );
        }

        $table_prefix = sanitize_text_field( $_POST['table_prefix'] );
        update_option( 'be-table-prefix', $table_prefix );

        wp_send_json_success( __( 'Table prefix saved successfully', 'bulk-product-import' ) );
    }

    public function save_profit_percentage_callback() {

        global $wpdb;

        // Uncomment this if you're using a nonce for security
        // check_ajax_referer( 'bulk_product_import_nonce', 'nonce' );

        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Unauthorized user', 'bulk-product-import' ) );
        }

        // Retrieve and sanitize the profit percentage input from the dashboard
        $profit_percentage = sanitize_text_field( $_POST['profit_percentage'] );

        // Save the profit percentage to the options table for future use
        update_option( 'be-profit-percentage', $profit_percentage );

        // Ensure profit_percentage is a valid number
        $profit_percentage = (float) $profit_percentage;
        $this->put_program_logs( 'Profit percentage: ' . $profit_percentage );
        if ( $profit_percentage < 0 ) {
            wp_send_json_error( __( 'Invalid profit percentage value', 'bulk-product-import' ) );
        }

        // Update product prices dynamically based on the profit percentage
        // Correct table name without the extra `wp_`
        $table_name = $wpdb->prefix . 'postmeta';

        // SQL Query: Cast meta_value to a float, apply the percentage increase
        $sql = $wpdb->prepare(
            "UPDATE {$table_name} 
            SET meta_value = CAST(meta_value AS DECIMAL(10,2)) * (1 + %f / 100) 
            WHERE meta_key = '_price' 
            AND meta_value > 0",
            $profit_percentage
        );

        // Execute the query
        $result = $wpdb->query( $sql );

        // Check if the query executed successfully
        if ( false === $result ) {
            wp_send_json_error( __( 'Failed to update product prices', 'bulk-product-import' ) );
        }

        wp_send_json_success( __( 'Profit percentage saved and prices updated successfully', 'bulk-product-import' ) );
    }

}
