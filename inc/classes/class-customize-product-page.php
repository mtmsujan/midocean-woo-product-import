<?php

namespace BULK_IMPORT\Inc;

use BULK_IMPORT\Inc\Traits\Singleton;

class Customize_Product_Page {

    use Singleton;

    public function __construct() {
        $this->setup_hooks();
    }

    public function setup_hooks() {
        // setup hooks
        add_shortcode( 'customize_product_page', [ $this, 'custom_product_page_callback' ] );
    }

    public function custom_product_page_callback() {

        // get product
        global $product;

        if ( $product ) {
            // get product id
            $product_id = $product->get_id();
            // get product meta
            $master_code = get_post_meta( $product_id, '_master_code', true );
        }

        ob_start();
        ?>

        <div id="accordion">
            <h3><?php esc_html_e( 'Details', 'bulk-product-import' ); ?></h3>
            <div class="additional-information-details">
                <div class="dimensions">
                    <h3><?php esc_html_e( 'Dimensions', 'bulk-product-import' ); ?></h3>
                    <div class="attribute-label">Dimensions: <span class="attribute-value">42 x 30 cm</span></div>
                </div>
                <div class="packaging">
                    <h3><?php esc_html_e( 'Packaging', 'bulk-product-import' ); ?></h3>
                    <div class="attribute-label">Carton Height: <span class="attribute-value">0.5 m</span></div>
                </div>
                <div class="general">
                    <h3><?php esc_html_e( 'General', 'bulk-product-import' ); ?></h3>
                    <div class="attribute-label">Main material: <span class="attribute-value">Straw</span></div>
                </div>
            </div>
            <h3><?php esc_html_e( 'Documentation & certificates', 'bulk-product-import' ); ?></h3>
            <div>
                <p>Sed non urna. Donec et ante. Phasellus eu ligula. Vestibulum sit amet purus. Vivamus hendrerit, dolor at
                    aliquet laoreet, mauris turpis porttitor velit, faucibus interdum tellus libero ac justo. Vivamus non quam.
                    In suscipit faucibus urna. </p>
            </div>
        </div>

        <?php return ob_get_clean();
    }

}