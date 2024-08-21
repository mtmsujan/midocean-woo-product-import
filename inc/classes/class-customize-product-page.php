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
            <h3>Details</h3>
            <div>
                <p>Mauris mauris ante, blandit et, ultrices a, suscipit eget, quam. Integer ut neque. Vivamus nisi metus,
                    molestie vel, gravida in, condimentum sit amet, nunc. Nam a nibh. Donec suscipit eros. Nam mi. Proin viverra
                    leo ut odio. Curabitur malesuada. Vestibulum a velit eu ante scelerisque vulputate.</p>
                <?php echo 'Master Code: ' . $master_code; ?>
            </div>
            <h3>Documentation & certificates</h3>
            <div>
                <p>Sed non urna. Donec et ante. Phasellus eu ligula. Vestibulum sit amet purus. Vivamus hendrerit, dolor at
                    aliquet laoreet, mauris turpis porttitor velit, faucibus interdum tellus libero ac justo. Vivamus non quam.
                    In suscipit faucibus urna. </p>
            </div>
        </div>

        <?php return ob_get_clean();
    }

}