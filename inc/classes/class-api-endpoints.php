<?php

namespace BULK_IMPORT\Inc;

use BULK_IMPORT\Inc\Traits\Singleton;

class Api_Endpoints {

    use Singleton;

    public function __construct() {
        $this->setup_hooks();
    }

    public function setup_hooks() {
        add_action( 'rest_api_init', [ $this, 'register_api_endpoints' ] );
    }

    public function register_api_endpoints() {

        // server status
        register_rest_route( 'bulk-import/v1', '/server-status', [
            'methods'  => 'GET',
            'callback' => [ $this, 'bulk_product_import_callback' ],
        ] );

        // delete products from woocommerce
        register_rest_route( 'bulk-import/v1', '/delete-products', [
            'methods'  => 'GET',
            'callback' => [ $this, 'bulk_product_delete_callback' ],
        ] );

        // delete trash products from woocommerce
        register_rest_route( 'bulk-import/v1', '/delete-trash-products', [
            'methods'  => 'GET',
            'callback' => [ $this, 'bulk_product_trash_delete_callback' ],
        ] );

        // delete categories from woocommerce
        register_rest_route( 'bulk-import/v1', '/delete-woo-cats', [
            'methods'  => 'GET',
            'callback' => [ $this, 'delete_woo_cats_callback' ],
        ] );

        // test wp cron jobs
        register_rest_route( 'bulk-import/v1', '/test-job', [
            'methods'  => 'GET',
            'callback' => [ $this, 'test_wp_jobs' ],
        ] );
    }

    public function bulk_product_import_callback() {
        return $this->server_status_check();
    }

    private function server_status_check() {
        // For now, returning a sample response.
        return new \WP_REST_Response( [
            'success' => true,
            'message' => 'Server is up and running.',
        ], 200 );
    }

    public function bulk_product_delete_callback() {
        return $this->bulk_products_delete();
    }

    private function bulk_products_delete() {
        // Define arguments to query all products
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => 100,
        );

        // Retrieve all products based on the query arguments
        $products = get_posts( $args );

        // Loop through each product and delete it
        foreach ( $products as $product ) {
            wp_delete_post( $product->ID, true ); // Set the second parameter to true to bypass the trash and delete permanently
        }

        // Return a message indicating that all WooCommerce products have been deleted
        return new \WP_REST_Response( [
            'success' => true,
            'message' => 'Products have been deleted.',
        ] );
    }

    public function bulk_product_trash_delete_callback() {
        return $this->bulk_products_trash_delete();
    }

    private function bulk_products_trash_delete() {
        // Define arguments to query all trashed products
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'post_status'    => 'trash',
        );

        // Retrieve all trashed products based on the query arguments
        $trashed_products = get_posts( $args );

        // Loop through each trashed product and delete it permanently
        foreach ( $trashed_products as $product ) {
            wp_delete_post( $product->ID, true ); // Set the second parameter to true to bypass the trash and delete permanently
        }

        // Return a message indicating that all trashed WooCommerce products have been permanently deleted
        return new \WP_REST_Response( [
            'success' => true,
            'message' => 'Trashed products have been permanently deleted.',
        ] );
    }

    public function delete_woo_cats_callback() {
        return $this->delete_woo_cats();
    }

    private function delete_woo_cats() {
        // Get all categories from 'product_cat' taxonomy
        $categories = get_terms(
            array(
                'taxonomy'   => 'product_cat',
                'hide_empty' => false,
            )
        );

        if ( !empty( $categories ) && !is_wp_error( $categories ) ) {
            foreach ( $categories as $category ) {
                wp_delete_term( $category->term_id, 'product_cat' );
            }

            return new \WP_REST_Response( [
                'success' => true,
                'message' => 'All product categories have been deleted.',
            ] );

        } else {
            return new \WP_REST_Response( [
                'success' => false,
                'message' => 'Error occurred. Please try again.',
            ] );
        }
    }

    public function test_wp_jobs() {
        // generate random uid
        $uid = uniqid();
        return $this->put_program_logs( $uid );
    }

    public function put_program_logs( $data ) {
        // Ensure the directory for logs exists
        $directory = BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/program_logs/';
        if ( !file_exists( $directory ) ) {
            mkdir( $directory, 0777, true );
        }

        // Construct the log file path
        $file_name = $directory . 'program_logs.log';

        // Append the current datetime to the log entry
        $current_datetime = date( 'Y-m-d H:i:s' );
        $data             = $data . ' - ' . $current_datetime;

        // Write the log entry to the file
        if ( file_put_contents( $file_name, $data . "\n\n", FILE_APPEND | LOCK_EX ) !== false ) {
            return "Data appended to file successfully.";
        } else {
            return "Failed to append data to file.";
        }
    }
}
