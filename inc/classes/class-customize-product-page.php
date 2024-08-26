<?php

namespace BULK_IMPORT\Inc;

use BULK_IMPORT\Inc\Traits\Singleton;

class Customize_Product_Page {

    use Singleton;

    private $product_number;
    private $product_name;
    private $attachment_id;
    private $product_small_image;
    private $category_id;
    private $category_url;
    private $master_code;
    private $product_stock;
    private $number_of_print_positions;
    private $color_group;

    public function __construct() {
        $this->setup_hooks();
    }

    public function setup_hooks() {
        // setup hooks
        add_shortcode( 'display_product_info', [ $this, 'display_product_info_callback' ] );
        add_shortcode( 'display_product_sku', [ $this, 'display_product_sku_callback' ] );
        add_shortcode( 'custom_product_configurator', [ $this, 'custom_product_configurator_callback' ] );
        add_shortcode( 'custom_product_configurator_mto_link', [ $this, 'custom_product_configurator_mto_link_callback' ] );
    }

    public function display_product_info_callback() {

        global $product;

        if ( $product ) {

            // get product id
            $product_id = $product->get_id();
            // Get product number
            $this->product_number = $product->get_sku();
            // Get product name
            $this->product_name = $product->get_name();
            // Get attachment id
            $this->attachment_id = get_post_thumbnail_id( $product_id );
            // Get product 150x150 image url
            $this->product_small_image = wp_get_attachment_image_src( $this->attachment_id, 'thumbnail' )[0];
            // Get category id
            $this->category_id = get_the_terms( $product_id, 'product_cat' )[0]->term_id;
            // Get category url
            $this->category_url = get_term_link( $this->category_id, 'product_cat' );
            // Get product stock
            $this->product_stock = get_post_meta( $product_id, '_stock', true );
            // Get number of print positions
            $this->number_of_print_positions = get_post_meta( $product_id, '_number_of_print_positions', true );
            // Get color group
            $this->color_group = get_post_meta( $product_id, '_color_group', true );

            // get product data
            // $product_data = $this->fetch_product_data_from_db( $this->product_number );
            // put product data to log
            // $this->put_program_logs( $product_data );

            // Retrieve all metadata
            $master_code               = get_post_meta( $product_id, '_master_code', true );
            $this->master_code         = $master_code;
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
            <h3><?php esc_html_e( 'Detalles del producto', 'bulk-product-import' ); ?></h3>
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
            <h3><?php esc_html_e( 'Documentación y Certificados', 'bulk-product-import' ); ?></h3>
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
                        <div>
                            <!-- Color input row configurator. repeater -->
                            <div class="color-input-container">
                                <div class="row mt-3 justify-content-between align-items-center color-input-row">
                                    <!-- Color dropdown -->
                                    <div class="col-sm-2">
                                        <div class=" color-dropdown-wrapper">
                                            <!-- First displayed button -->
                                            <button class="" type="button" id="colorDropdown" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <div class="row align-items-center">
                                                    <div class="col-4 color-preview">
                                                        <div style="height: 30px; width: 30px; background: <?php echo strtolower( $this->color_group ); ?>"
                                                            class="rounded-circle"></div>
                                                    </div>
                                                    <div class="col-8 color-name"><?php echo ucfirst( $this->color_group ); ?> -
                                                    </div>
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                    <!-- /Color dropdown -->
                                    <!-- Stock input field -->
                                    <div class="col-sm-6">
                                        <div class="row flex-column">
                                            <div class="col-sm-12">
                                                <div class="input-quantity-wrapper">
                                                    <input @change="calculateTotal($el.value, selectedColor.stock)"
                                                        type="number" class="input-quantity" id="" name="" step="1" min="1"
                                                        max="100000" placeholder="0">
                                                </div>
                                            </div>
                                            <div class="col-sm-12 mt-1">
                                                <div class="row justify-content-around align-items-center">
                                                    <div class="col-4">
                                                        <div class="stock-data">
                                                            <div><span class="stock-value">
                                                                    <?php echo $this->product_stock; ?></span>En Stock</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-8"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /Stock input field -->
                                    <!-- Remove color row button -->
                                    <div class="col-sm-4 remove-color-row">
                                        <div class="row justify-content-between align-items-center">
                                            <div class="col-sm-6"></div>
                                            <div class="col-sm-6 text-end close-button"><i class="fa-solid fa-xmark"></i></div>
                                        </div>
                                    </div>
                                    <!-- /Remove color row button -->
                                </div>
                            </div>
                        </div>
                        <!-- Add more color button -->
                        <!-- <div class="buttons mt-3">
                            <button :disabled="!isColorAvailable" @click="addColor" id="add-more-colors-button"
                                class="row justify-content-between add-more-colors-button align-items-center pe-2">
                                <div class="col-10 button-text">
                                    <?php // esc_html_e( 'Añadir más colores', 'bulk-product-import' ) ?>
                                </div>
                                <div class="col-2 button-add-icon"><i class="fa-solid fa-plus"></i></div>
                            </button>
                        </div> -->
                        <!-- /Add more color button -->

                        <?php

                        /**
                         * Check if user logged in and number of print positions is more than 0
                         */

                        if ( $this->number_of_print_positions > 0 ) : ?>

                            <!-- Add print position -->
                            <div class="add-print-position mt-5">
                                <div class="add-print-position-header">
                                    <h3 class="add-print-position-heading">
                                        <?php esc_html_e( 'Posiciones impresión', 'bulk-product-import' ) ?>
                                    </h3>
                                </div>
                                <div class="add-print-position-body">
                                    <!-- print positions -->
                                    <div class="print-positions mt-2">
                                        <!-- print position REPEAT:-->
                                        <div class="print-position pb-2 ms-3" data-name="FRONT">
                                            <div class="technique-wrapper row align-items-center justify-content-evenly"
                                                data-technique-code="T1">
                                                <div class="thumb-wrapper col-sm-4 border-right me-2">
                                                    <div class="row align-items-center">
                                                        <div class="thumb-image col-4">
                                                            <img class="thumb"
                                                                src="https://printtemplates-v2.cdn.midocean.com/756d7bde-5794-4f3d-7e9a-08dbe69b9e8f_13_202407261021569267-print-position-variant-thumbnail">
                                                        </div>
                                                        <div class="position-info col-8">
                                                            <div class="position-name">
                                                                <span class="position-name-serial">1. </span>FRONT
                                                            </div>
                                                            <div class="position-infos">
                                                                <span>Transfer serigráfico</span>
                                                                <span>Colores máximos : <span class="color-count">8</span></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="size-wrapper col">
                                                    <div class="row align-items-center justify-content-between">
                                                        <div class="col bg-white border-right">
                                                            <span class="input-title">Ancho (mm)</span>
                                                            <input type="number" class="print-position-number" size="7" min="0"
                                                                value="60" data-max="60">
                                                        </div>
                                                        <div class="col bg-white">
                                                            <span class="input-title">Ancho (mm)</span>
                                                            <input type="number" class="print-position-number" size="7" min="0"
                                                                value="60" data-max="60">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="colors-wrapper col-sm-2">
                                                    <select name="" id="">
                                                        <option value="1">1 Colors</option>
                                                    </select>
                                                </div>
                                                <div class="remove-wrapper col-sm-2">
                                                    <div class="remove-button">
                                                        <i class="fa-solid fa-xmark"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /print position REPEAT:-->
                                    </div>
                                    <!-- /print positions -->
                                </div>
                                <!-- Add print position button -->
                                <div class="add-print-position-button-portion">
                                    <div class="mt-3">
                                        <button class="add-print-position-button" id="add-print-position-button"
                                            class="row justify-content-between add-more-colors-button align-items-center pe-2"
                                            data-toggle="modal" data-target="#add_print_position_modal_button">
                                            <div class="col-10 button-text p-0">
                                                <?php esc_html_e( 'Añadir posición de impresión', 'bulk-product-import' ) ?>
                                            </div>
                                            <div class="col-2 button-add-icon"><i class="fa-solid fa-plus"></i></div>
                                        </button>
                                    </div>
                                </div>
                                <!-- /Add print position button -->
                                <!-- Modal -->
                                <div class="modal fade" id="add_print_position_modal_button" tabindex="-1" role="dialog"
                                    aria-labelledby="add_print_position_modal_buttonTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle">
                                                    <?php esc_html_e( ' Añadir posición de impresión ', 'bulk-product-import' ) ?>
                                                </h5>
                                                <button type="button" class="modal-close-icon" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <!-- <span aria-hidden="true">&times;</span> -->
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                ...
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="modal-close-button"
                                                    data-dismiss="modal"><?php esc_html_e( 'Cancelar', 'bulk-product-import' ) ?></button>
                                                <button type="button"
                                                    class="modal-save-button"><?php esc_html_e( 'Añadir', 'bulk-product-import' ) ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Add print position -->
                        <?php endif; ?>

                    </div>
                    <div class="col-sm-4 product-configurator-body-right-portion">
                        <div class="pricing-summery">
                            <div class="summery-header">
                                <img src="<?php echo $this->product_small_image; ?>"
                                    alt="<?php echo $this->product_name; ?> product photo" height="75" width="75">
                                <div class="name-wrapper">
                                    <span class="main-text"><?php echo $this->product_number; ?></span>
                                    <span class="sub-text"><?php echo $this->product_name; ?></span>
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

        <?php
        return ob_get_clean();
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
                    <img src="<?php echo $this->product_small_image; ?>" alt="<?php echo $this->product_name; ?> product photo">
                    <div class="details">
                        <div class="details-text">Selecciona un modelo de stock para una entrega más rápida, o haz clic aquí
                            para obtener gorros de
                            cubo impresos a todo color. A partir de 25 unidades.</div>
                        <div class="sub-details"></div>
                        <a href="<?php echo $this->category_url; ?>" target="_blank">Ver más detalles
                            <span class="arrow-icon"><i class="fa-solid fa-arrow-right"></i></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <?php return ob_get_clean();
    }

    public function fetch_product_data_from_db( $product_number ) {
        // Get global $wpdb object
        global $wpdb;

        // get table prefix
        $table_prefix = get_option( 'be-table-prefix' ) ?? '';

        // define products table
        $products_table = $wpdb->prefix . $table_prefix . 'sync_products';

        // define price table
        $price_table = $wpdb->prefix . $table_prefix . 'sync_price';

        // define stock table
        $stock_table = $wpdb->prefix . $table_prefix . 'sync_stock';

        // SQL query to retrieve pending products
        $sql = "SELECT sp.id, sp.product_number, sp.product_data, ss.stock, spr.variant_id, spr.price, spr.valid_until, sp.status  FROM $products_table sp JOIN $stock_table ss ON sp.product_number = ss.product_number JOIN $price_table spr ON sp.product_number = spr.product_number WHERE sp.product_number = '{$product_number}'";

        // Retrieve pending products from the database
        $products = $wpdb->get_results( $wpdb->prepare( $sql ) );

        return json_encode( $products );
    }

    public function fetch_product_print_data_from_db( $master_code ) {

        // Get global $wpdb object
        global $wpdb;

        // get table prefix
        $table_prefix = get_option( 'be-table-prefix' ) ?? '';
        $table_name   = $wpdb->prefix . $table_prefix . 'sync_products_print_data';

        // SQL Query
        $sql = "SELECT print_data FROM $table_name WHERE master_code = '{$master_code}'";
        // Execute query
        $print_data = $wpdb->get_results( $wpdb->prepare( $sql ) );
        // Get print data
        $print_data = $print_data[0]->print_data;

        // Return print data
        return json_encode( $print_data );
    }

    public function put_program_logs( $data ) {

        // Ensure directory exists to store response data
        $directory = BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/program_logs/';
        if ( !file_exists( $directory ) ) {
            mkdir( $directory, 0777, true );
        }

        // Construct file path for response data
        $file_name = $directory . 'program_logs.log';

        // Get the current date and time
        $current_datetime = date( 'Y-m-d H:i:s' );

        // Append current date and time to the response data
        $data = $data . ' - ' . $current_datetime;

        // Append new response data to the existing file
        if ( file_put_contents( $file_name, $data . "\n\n", FILE_APPEND | LOCK_EX ) !== false ) {
            return "Data appended to file successfully.";
        } else {
            return "Failed to append data to file.";
        }
    }

}