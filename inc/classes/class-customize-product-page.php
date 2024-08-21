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

        <div id="additional-information-accordion">
            <h3><?php esc_html_e( 'Details', 'bulk-product-import' ); ?></h3>
            <div class="additional-information-details">
                <div class="dimensions">
                    <h3 class="details-title"><?php esc_html_e( 'Dimensions', 'bulk-product-import' ); ?></h3>
                    <div class="attribute-label">Dimensions: <span class="attribute-value">42 x 30 cm</span></div>
                </div>
                <div class="packaging">
                    <h3 class="details-title"><?php esc_html_e( 'Packaging', 'bulk-product-import' ); ?></h3>
                    <div class="attribute-label">Carton Height: <span class="attribute-value">0.5 m</span></div>
                </div>
                <div class="general">
                    <h3 class="details-title"><?php esc_html_e( 'General', 'bulk-product-import' ); ?></h3>
                    <div class="attribute-label">Main material: <span class="attribute-value">Straw</span></div>
                </div>
            </div>
            <h3><?php esc_html_e( 'Documentation & certificates', 'bulk-product-import' ); ?></h3>
            <div class="additional-information-documentation">
                <div class="documentation">
                    <div class="doc-item">
                        <span>👁</span>
                        <a href="https://midoceanbrands.ma.informationstore.net/informationstore/F/F0072553_0001.pdf?expires=99990909000000&amp;secretname=InformationStore&amp;id=0&amp;ticket=50C6FBAEA661F8AEFA7B28D4ECF7DC70"
                            target="_blank" rel="noopener noreferrer">Declaration of Conformity</a>
                    </div>
                    <span>⭳</span>
                </div>
            </div>
        </div>

        <?php return ob_get_clean();
    }

}