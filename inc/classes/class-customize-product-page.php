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
        add_shortcode( 'display_product_info', [ $this, 'custom_product_page_callback' ] );
        add_shortcode( 'display_product_sku', [ $this, 'display_product_sku_callback' ] );
        add_shortcode( 'custom_product_configurator', [ $this, 'custom_product_configurator_callback' ] );
        add_shortcode( 'custom_product_configurator_mto_link', [ $this, 'custom_product_configurator_mto_link_callback' ] );
    }

    public function custom_product_page_callback() {

        global $product;

        if ( $product ) {
            // get product id
            $product_id = $product->get_id();

            // Retrieve all metadata
            $master_code               = get_post_meta( $product_id, '_master_code', true );
            $master_id                 = get_post_meta( $product_id, '_master_id', true );
            $country_of_origin         = get_post_meta( $product_id, '_country_of_origin', true );
            $type_of_products          = get_post_meta( $product_id, '_type_of_products', true );
            $commodity_code            = get_post_meta( $product_id, '_commodity_code', true );
            $number_of_print_positions = get_post_meta( $product_id, '_number_of_print_positions', true );
            $brand                     = get_post_meta( $product_id, '_brand', true );
            $product_class             = get_post_meta( $product_id, '_product_class', true );
            $length                    = get_post_meta( $product_id, '_length', true );
            $length_unit               = get_post_meta( $product_id, '_length_unit', true );
            $width                     = get_post_meta( $product_id, '_width', true );
            $width_unit                = get_post_meta( $product_id, '_width_unit', true );
            $height                    = get_post_meta( $product_id, '_height', true );
            $height_unit               = get_post_meta( $product_id, '_height_unit', true );
            $volume                    = get_post_meta( $product_id, '_volume', true );
            $volume_unit               = get_post_meta( $product_id, '_volume_unit', true );
            $gross_weight              = get_post_meta( $product_id, '_gross_weight', true );
            $gross_weight_unit         = get_post_meta( $product_id, '_gross_weight_unit', true );
            $net_weight                = get_post_meta( $product_id, '_net_weight', true );
            $net_weight_unit           = get_post_meta( $product_id, '_net_weight_unit', true );
            $outer_carton_quantity     = get_post_meta( $product_id, '_outer_carton_quantity', true );
            $carton_length             = get_post_meta( $product_id, '_carton_length', true );
            $carton_length_unit        = get_post_meta( $product_id, '_carton_length_unit', true );
            $carton_width              = get_post_meta( $product_id, '_carton_width', true );
            $carton_width_unit         = get_post_meta( $product_id, '_carton_width_unit', true );
            $carton_height             = get_post_meta( $product_id, '_carton_height', true );
            $carton_height_unit        = get_post_meta( $product_id, '_carton_height_unit', true );
            $carton_volume             = get_post_meta( $product_id, '_carton_volume', true );
            $carton_gross_weight_unit  = get_post_meta( $product_id, '_carton_gross_weight_unit', true );
            $material                  = get_post_meta( $product_id, '_material', true );
            $category_label1           = get_post_meta( $product_id, '_category_level1', true );
            $category_label2           = get_post_meta( $product_id, '_category_level2', true );
            $category_label3           = get_post_meta( $product_id, '_category_level3', true );
            $color_description         = get_post_meta( $product_id, '_color_description', true );
            $color_group               = get_post_meta( $product_id, '_color_group', true );
            $pcl_status_description    = get_post_meta( $product_id, '_pcl_status_description', true );
            $pms_color                 = get_post_meta( $product_id, '_pms_color', true );
            $ean                       = get_post_meta( $product_id, '_ean', true ) ?? '';

            // Generate dimensions by width and height with unit
            $dimensions = $width . ' x ' . $height . ' ' . $height_unit;
            // Generate diameter with width and unit
            $diameter = $width . ' ' . $width_unit;
            // Generate width with unit
            $width = $width . ' ' . $width_unit;
            // Generate height with unit
            $height = $height . ' ' . $height_unit;
            // Generate length with unit
            $length = $length . ' ' . $length_unit;
            // Generate volume with unit
            $volume = $volume . ' ' . $volume_unit;
            // Generate gross weight with unit
            $gross_weight = $gross_weight . ' ' . $gross_weight_unit;
            // Generate net weight with unit
            $net_weight = $net_weight . ' ' . $net_weight_unit;
            // Generate carton height with unit
            $carton_height = $carton_height . ' ' . $carton_height_unit;
            // Generate carton width with unit
            $carton_width = $carton_width . ' ' . $carton_width_unit;
            // Generate carton length with unit
            $carton_length = $carton_length . ' ' . $carton_length_unit;
            // Generate carton volume with unit
            $carton_volume = $carton_volume . ' ' . $volume_unit;

            // Retrieve digital_assets and decode JSON
            $digital_assets = get_post_meta( $product_id, '_digital_assets', true );
            $digital_assets = json_decode( $digital_assets, true );
        }

        ob_start();
        ?>

        <div id="additional-information-accordion">
            <h3><?php esc_html_e( 'Details', 'bulk-product-import' ); ?></h3>
            <div class="additional-information-details">
                <div class="dimensions">
                    <h3 class="details-title"><?php esc_html_e( 'Dimensions', 'bulk-product-import' ); ?></h3>
                    <div class="attribute-label">Dimensions: <span class="attribute-value"><?php echo $dimensions; ?></span>
                    </div>
                    <div class="attribute-label">Width: <span class="attribute-value"><?php echo $width; ?></span></div>
                    <div class="attribute-label">Height: <span class="attribute-value"><?php echo $height; ?></span></div>
                    <div class="attribute-label">Length: <span class="attribute-value"><?php echo $length; ?></span></div>
                    <div class="attribute-label">Diameter: <span class="attribute-value"><?php echo $diameter; ?></span></div>
                    <div class="attribute-label">Volume: <span class="attribute-value"><?php echo $volume; ?></span></div>
                    <div class="attribute-label">Gross Weight: <span class="attribute-value"><?php echo $gross_weight; ?></span>
                    </div>
                    <div class="attribute-label">Net Weight: <span class="attribute-value"><?php echo $net_weight; ?></span>
                    </div>
                </div>
                <div class="packaging">
                    <h3 class="details-title"><?php esc_html_e( 'Packaging', 'bulk-product-import' ); ?></h3>
                    <div class="attribute-label">Carton Height: <span
                            class="attribute-value"><?php echo $carton_height; ?></span></div>
                    <div class="attribute-label">Carton Width: <span class="attribute-value"><?php echo $carton_width; ?></span>
                    </div>
                    <div class="attribute-label">Carton Length: <span
                            class="attribute-value"><?php echo $carton_length; ?></span></div>
                    <div class="attribute-label">Carton Volume: <span
                            class="attribute-value"><?php echo $carton_volume; ?></span></div>
                    <div class="attribute-label">Carton Quantity: <span
                            class="attribute-value"><?php echo $outer_carton_quantity . ' pieces'; ?></span></div>
                </div>
                <div class="general">
                    <h3 class="details-title"><?php esc_html_e( 'General', 'bulk-product-import' ); ?></h3>
                    <div class="attribute-label">Main material: <span class="attribute-value"><?php echo $material; ?></span>
                    </div>
                    <div class="attribute-label">Commodity Code: <span
                            class="attribute-value"><?php echo $commodity_code; ?></span></div>
                    <div class="attribute-label">Country of Origin: <span
                            class="attribute-value"><?php echo $country_of_origin; ?></span></div>
                    <div class="attribute-label">EAN: <span class="attribute-value"><?php echo $ean; ?></span></div>
                    <div class="attribute-label">PMS Color: <span class="attribute-value"><?php echo $pms_color; ?></span>
                    </div>
                </div>
            </div>
            <h3><?php esc_html_e( 'Documentation & certificates', 'bulk-product-import' ); ?></h3>
            <div class="additional-information-documentation">
                <div class="documentation">
                    <?php
                    if ( !empty( $digital_assets ) && is_array( $digital_assets ) ) :
                        foreach ( $digital_assets as $asset ) :
                            $url           = esc_url( $asset['url'] );
                            $subtype       = str_replace( '_', ' ', $asset['subtype'] );
                            $subtype_label = ucwords( $subtype );

                            // Check if it's a .zip file
                            $icon = ( strpos( $url, '.zip' ) !== false ) ? '⭳' : '👁';

                            // Output the link
                            ?>
                            <div class="doc-item">
                                <span>
                                    <?php echo $icon; ?>
                                </span>
                                <a href="<?php echo $url; ?>" target="_blank" rel="noopener noreferrer">
                                    <?php echo $subtype_label; ?>
                                </a>
                            </div>
                            <?php
                        endforeach;
                    else :
                        ?>
                        <p>
                            <?php esc_html_e( 'No documentation or certificates available.', 'bulk-product-import' ); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php return ob_get_clean();
    }

    public function display_product_sku_callback() {
        // global product
        global $product;

        if ( $product ) {
            // product sku
            $sku = $product->get_sku();
        }

        printf( '<h1 class="be-product-sku">%s</h1>', $sku );
    }

    public function custom_product_configurator_callback() {
        ob_start();
        ?>

        <div class="product-configurator-row pb-5">
            <div class="product-configurator-heading">
                <div class="row align-items-end justify-content-between pb-2 product-configurator-heading-portion">
                    <div class="col-sm-8">
                        <div class="be-title"><?php esc_html_e( 'Configurador artículo', 'bulk-product-import' ); ?></div>
                    </div>
                    <div class="col-sm-4">
                        <a class="d-block product-configurator-download-button"
                            href="#"><?php esc_html_e( 'Descarga la hoja del producto', 'bulk-product-import' ); ?> <i
                                class="fa-solid fa-arrow-down ms-2"></i></a>
                        <a class="d-block product-configurator-save-button"
                            href="#"><?php esc_html_e( 'Guarda esta configuración como concepto', 'bulk-product-import' ) ?> <i
                                class="fa-solid fa-floppy-disk ms-2"></i></a>
                    </div>
                </div>
            </div>
            <div class="product-configurator-body">
                <div class="row">
                    <div class="col-sm-8 product-configurator-body-left-portion">
                        <div class="product-configurator-body-subheading-div">
                            <h3 class="product-configurator-body-subheading">
                                <?php esc_html_e( 'Variaciones artículo', 'bulk-product-import' ) ?>
                            </h3>
                        </div>
                    </div>
                    <div class="col-sm-4 product-configurator-body-right-portion">
                        <div class="pricing-summery">
                            <div class="summery-header">
                                <img src="https://cdn1.midocean.com/image/75X75/mo2267-13.jpg" alt="MO2267-13 product photo"
                                    height="75" width="75">
                                <div class="name-wrapper">
                                    <span class="main-text">MO2267</span>
                                    <span class="sub-text">BILGOLA+</span>
                                </div>
                            </div>
                            <div class="summary-row underline shipping">
                                <div><?php esc_html_e( 'Portes web península (oficina 20 eur)', 'bulk-product-import' ) ?></div>
                                <div class="value" data-default="-">-</div>
                            </div>
                            <div class="summary-row product-price">
                                <div class="text" data-default="Precio artículo">
                                    <?php esc_html_e( 'Precio artículo', 'bulk-product-import' ) ?>
                                </div>
                                <div class="value" data-default="-">-</div>
                            </div>
                            <div class="grand-totals underline">
                                <div class="summary-row grand-total">
                                    <div><?php esc_html_e( 'Total (incl. transporte)', 'bulk-product-import' ) ?></div>
                                    <div class="total" data-default="-">-</div>
                                </div>
                                <div class="summary-row price-per-item">
                                    <div class="price-per-item-subheading">
                                        <?php esc_html_e( 'Precio por artículo', 'bulk-product-import' ) ?>
                                    </div>
                                    <div class="value" data-default="-">-</div>
                                </div>
                            </div>
                        </div>
                        <div class="personalize-button">
                            <div class="button-wrapper">
                                <div>
                                    <a href="#"><?php esc_html_e( 'Personalizar', 'bulk-product-import' ) ?></a>
                                </div>
                                <div>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php return ob_get_clean();
    }

    public function custom_product_configurator_mto_link_callback() {
        ob_start();
        ?>

        <div id="productDetailPage">
            <div class="mto-link">
                <div class="heading-wrapper">
                    <h3>Opción yourChoice totalmente personalizable</h3>
                </div>
                <div class="detail-wrapper">
                    <img
                        src="https://www.midocean.com/INTERSHOP/static/WFS/midocean-IB-Site/-/midocean/es_ES/mto-yourchoice/MTO-link/MTS%20to%20MTO%20bucket%20hats%20yourChoice.jpg">
                    <div class="details">
                        <div class="details-text">Selecciona un modelo de stock para una entrega más rápida, o haz clic aquí para obtener gorros de
                            cubo impresos a todo color. A partir de 25 unidades.</div>
                            <div class="sub-details"></div>
                            <a href="https://www.midocean.com/iberia/es/eur/content/page.yourHeadwear.BucketHats" target="_blank">Ver más detalles
                                <span class="arrow-icon"><i class="fa-solid fa-arrow-right"></i></span>
                            </a>
                    </div>
                </div>
            </div>
        </div>

        <?php return ob_get_clean();
    }

}