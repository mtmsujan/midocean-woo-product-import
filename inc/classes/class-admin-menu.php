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

                        <button type="button" class="btn btn-primary mt-3 d-flex align-items-center justify-content-between gap-1" id="profit-percentage-save-button">
                            <span>Save</span>
                            <span class="ms-1 profit-percentage-spinner"></span>
                        </button>
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

        // Check for the necessary permissions before proceeding.
        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Unauthorized user', 'bulk-product-import' ) );
        }

        // Retrieve and sanitize the profit percentage input from the dashboard.
        $profit_percentage = sanitize_text_field( $_POST['profit_percentage'] );

        // Ensure that the profit percentage is a valid number and greater than or equal to zero.
        $profit_percentage = (float) $profit_percentage;
        if ( $profit_percentage < 0 ) {
            wp_send_json_error( __( 'Invalid profit percentage value. It must be 0 or greater.', 'bulk-product-import' ) );
        }

        // Save the profit percentage to the options table for future use.
        update_option( 'be-profit-percentage', $profit_percentage );

        // Define the postmeta table name.
        $table_name = $wpdb->prefix . 'postmeta';

        // Query to get all product IDs that have a '_price' meta key with a value greater than 0.
        $query_to_get_product_id = "SELECT DISTINCT post_id FROM {$table_name} WHERE meta_key = '_price' AND meta_value > 0";
        $all_product_id          = $wpdb->get_results( $query_to_get_product_id );

        // Check if any product IDs were found.
        if ( !empty( $all_product_id ) ) {
            foreach ( $all_product_id as $product_id ) {
                // Retrieve the product object using the WooCommerce function wc_get_product().
                $product = wc_get_product( $product_id->post_id );

                // Handle the case where the product could not be found (just in case).
                if ( !$product ) {
                    continue;
                }

                // Get the SKU for the current product.
                $sku = $product->get_sku();

                // Retrieve the original price from your custom DB function based on SKU.
                $product_db_price = $this->get_product_price_from_db( $sku );

                // Handle any errors or missing prices.
                if ( !is_numeric( $product_db_price ) || $product_db_price < 0 ) {
                    continue;
                }

                // Calculate the new price based on the profit percentage.
                $calculate_price = ( $profit_percentage / 100 ) * $product_db_price;
                $new_price       = $product_db_price + $calculate_price;

                // Update the product price in the '_price' meta field.
                $update_price = $wpdb->update(
                    $table_name,
                    array( 'meta_value' => $new_price ), // New price
                    array( 'post_id' => $product_id->post_id, 'meta_key' => '_price' ), // Conditions
                    array( '%f' ), // Format for the new price (float)
                    array( '%d', '%s' ) // Format for the WHERE clause (post_id as integer, meta_key as string)
                );

                // Check if the update query failed (returns false if failed).
                if ( false === $update_price ) {
                    // Log an error for debugging purposes (optional).
                    error_log( "Failed to update price for product ID: {$product_id->post_id}" );
                }
            }
        } else {
            wp_send_json_error( __( 'No products found with valid prices.', 'bulk-product-import' ) );
        }

        // Return a success message once all prices are updated.
        wp_send_json_success( __( 'Profit percentage saved and prices updated successfully.', 'bulk-product-import' ) );
    }

    public function get_product_price_from_db( $product_number ) {

        global $wpdb;

        // Get the table prefix option for dynamic table names
        $table_prefix = get_option( 'be-table-prefix' ) ?? '';

        // Define the full table name using the WordPress prefix and the dynamic table prefix
        $table_name = $wpdb->prefix . $table_prefix . 'sync_price';

        // Sanitize the product number to prevent SQL injection
        $product_number = sanitize_text_field( $product_number );

        // Prepare SQL query to retrieve the price from the sync_price table
        $sql = $wpdb->prepare( "SELECT price FROM {$table_name} WHERE product_number = %s", $product_number );

        // Execute the query
        $result = $wpdb->get_results( $sql );

        // Check if the result is not empty and contains at least one price value
        if ( !empty( $result ) && isset( $result[0]->price ) ) {
            // Get the price from the first row of the result
            $price = $result[0]->price;

            // Replace commas with dots to ensure price format consistency
            $price = str_replace( ",", ".", $price );

            // Return the price
            return $price;
        } else {
            // Return false if no price is found for the given product number
            return false;
        }
    }

}
