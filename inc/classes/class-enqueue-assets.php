<?php

namespace BULK_IMPORT\Inc;

defined( "ABSPATH" ) || exit( "Direct Access Not Allowed" );

use BULK_IMPORT\Inc\Traits\Singleton;

class Enqueue_Assets {

    use Singleton;

    public function __construct() {
        $this->setup_hooks();
    }

    public function setup_hooks() {
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_css' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_js' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_style' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_script' ] );
    }

    public function enqueue_css() {
        // Register CSS
        wp_register_style( "be-style", BULK_PRODUCT_IMPORT_ASSETS_PATH . "/css/be-style.css", );
        wp_register_style( "be-bootstrap", BULK_PRODUCT_IMPORT_ASSETS_PATH . "/css/bootstrap.min.css" );
        wp_register_style( "be-customize-product-page", BULK_PRODUCT_IMPORT_ASSETS_PATH . "/public/css/be-customize-product-page.css", [], time(), "all" );
        wp_register_style( "be-select2", BULK_PRODUCT_IMPORT_ASSETS_PATH . "/public/css/select2.min.css" );

        // enqueue font awesome
        wp_enqueue_style( "font-awesome-css", "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css", [], false, "all" );
        wp_enqueue_style( "jquery-ui-accordion-css", "https://code.jquery.com/ui/1.14.0/themes/base/jquery-ui.css", [], false, "all" );

        // enqueue CSS
        wp_enqueue_style( "be-style" );
        wp_enqueue_style( "be-bootstrap" );
        wp_enqueue_style( "be-customize-product-page" );
        wp_enqueue_style( "be-select2" );
    }

    public function enqueue_js() {

        // Register JS
        wp_register_script( "be-app", BULK_PRODUCT_IMPORT_ASSETS_PATH . "/js/app.js", [ 'jquery' ], false, true );
        wp_register_script( "be-popperjs", "https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js", [], false, true );
        wp_register_script( "be-bootstrap", "https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js", [ 'be-popperjs' ], false, true );
        wp_register_script( "be-alpine-js", "//unpkg.com/alpinejs", [], false, true );
        wp_register_script( "be-select2", BULK_PRODUCT_IMPORT_ASSETS_PATH . "/js/select2.full.min.js", ['jquery'], false, true );

        // enqueue JS
        wp_enqueue_script( "jquery-ui-core" );
        wp_enqueue_script( "jquery-ui-accordion" );

        wp_enqueue_script( "be-app" );
        // send ajax url
        wp_localize_script( "be-app", "bulkProductImport", [
            "ajax_url" => admin_url( "admin-ajax.php" ),
        ] );
        
        wp_enqueue_script( "be-popperjs" );
        wp_enqueue_script( "be-bootstrap" );
        wp_enqueue_script( "be-alpine-js" );
        wp_enqueue_script( "be-select2" );
    }

    public function admin_enqueue_style() {
        wp_register_style( "be-admin-bootstrap", BULK_PRODUCT_IMPORT_ASSETS_PATH . "/css/bootstrap.min.css" );
        wp_register_style( "be-admin-style", BULK_PRODUCT_IMPORT_ASSETS_PATH . "/css/be-admin.css" );
        wp_register_style( "be-admin-toastify", BULK_PRODUCT_IMPORT_ASSETS_PATH . "/css/toastify.css" );

        wp_enqueue_style( "be-admin-style" );
        wp_enqueue_style( "be-admin-bootstrap" );
        wp_enqueue_style( "be-admin-toastify" );
    }

    public function admin_enqueue_script() {
        // register confetti js
        wp_register_script( "be-confetti", BULK_PRODUCT_IMPORT_ASSETS_PATH . "/js/confetti.min.js", [], false, true );

        // toastify js
        wp_register_script( "toastify", BULK_PRODUCT_IMPORT_ASSETS_PATH . "/js/toastify.js", [], false, true );

        // register admin menu js
        wp_register_script( "be-admin-menu", BULK_PRODUCT_IMPORT_ASSETS_PATH . "/js/admin-menu.js", [ 'jquery' ], false, true );
        wp_localize_script( 'be-admin-menu', 'bulkProductImport', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'bulk_product_import_nonce' ),
        ] );

        wp_enqueue_script( "jquery-ui-tabs" );
        wp_enqueue_script( "be-confetti" );
        wp_enqueue_script( "be-admin-menu" );
        wp_enqueue_script( "toastify" );
    }
}