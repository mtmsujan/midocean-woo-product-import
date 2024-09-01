<?php

namespace BULK_IMPORT\Inc;

use BULK_IMPORT\Inc\Traits\Singleton;

class Customize_Product_Page {

    use Singleton;

    private $product_id;
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
    private $product_price;
    private $currency_symbol;

    public function __construct() {
        $this->setup_hooks();
    }

    public function setup_hooks() {
        // setup hooks
        add_shortcode( 'display_product_info', [ $this, 'display_product_info_callback' ] );
        add_shortcode( 'display_product_sku', [ $this, 'display_product_sku_callback' ] );
        add_shortcode( 'custom_product_configurator', [ $this, 'custom_product_configurator_callback' ] );
        add_shortcode( 'custom_product_configurator_mto_link', [ $this, 'custom_product_configurator_mto_link_callback' ] );
        add_action( 'wp_ajax_custom_add_to_cart', [ $this, 'custom_add_to_cart_handler' ] );
        add_action( 'wp_ajax_nopriv_custom_add_to_cart', [ $this, 'custom_add_to_cart_handler' ] );
    }

    public function display_product_info_callback() {

        global $product;

        if ( $product ) {

            // get product id
            $product_id       = $product->get_id();
            $this->product_id = $product_id;
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
            // Get product price
            $this->product_price = get_post_meta( $product_id, '_price', true );
            $this->product_price = str_replace( ',', '', $this->product_price );
            // Get currency symbol
            $this->currency_symbol = get_woocommerce_currency_symbol();

            // get product data
            // $product_data = $this->fetch_product_data_from_db( $this->product_number );
            // put product data to log
            // $this->put_program_logs( $product_data );

            // Retrieve all metadata
            $master_code              = get_post_meta( $product_id, '_master_code', true );
            $this->master_code        = $master_code;
            $master_id                = get_post_meta( $product_id, '_master_id', true );
            $country_of_origin        = get_post_meta( $product_id, '_country_of_origin', true );
            $type_of_products         = get_post_meta( $product_id, '_type_of_products', true );
            $commodity_code           = get_post_meta( $product_id, '_commodity_code', true );
            $brand                    = get_post_meta( $product_id, '_brand', true );
            $product_class            = get_post_meta( $product_id, '_product_class', true );
            $length                   = get_post_meta( $product_id, '_length', true );
            $length_unit              = get_post_meta( $product_id, '_length_unit', true );
            $width                    = get_post_meta( $product_id, '_width', true );
            $width_unit               = get_post_meta( $product_id, '_width_unit', true );
            $height                   = get_post_meta( $product_id, '_height', true );
            $height_unit              = get_post_meta( $product_id, '_height_unit', true );
            $volume                   = get_post_meta( $product_id, '_volume', true );
            $volume_unit              = get_post_meta( $product_id, '_volume_unit', true );
            $gross_weight             = get_post_meta( $product_id, '_gross_weight', true );
            $gross_weight_unit        = get_post_meta( $product_id, '_gross_weight_unit', true );
            $net_weight               = get_post_meta( $product_id, '_net_weight', true );
            $net_weight_unit          = get_post_meta( $product_id, '_net_weight_unit', true );
            $outer_carton_quantity    = get_post_meta( $product_id, '_outer_carton_quantity', true );
            $carton_length            = get_post_meta( $product_id, '_carton_length', true );
            $carton_length_unit       = get_post_meta( $product_id, '_carton_length_unit', true );
            $carton_width             = get_post_meta( $product_id, '_carton_width', true );
            $carton_width_unit        = get_post_meta( $product_id, '_carton_width_unit', true );
            $carton_height            = get_post_meta( $product_id, '_carton_height', true );
            $carton_height_unit       = get_post_meta( $product_id, '_carton_height_unit', true );
            $carton_volume            = get_post_meta( $product_id, '_carton_volume', true );
            $carton_gross_weight_unit = get_post_meta( $product_id, '_carton_gross_weight_unit', true );
            $material                 = get_post_meta( $product_id, '_material', true );
            $category_label1          = get_post_meta( $product_id, '_category_level1', true );
            $category_label2          = get_post_meta( $product_id, '_category_level2', true );
            $category_label3          = get_post_meta( $product_id, '_category_level3', true );
            $color_description        = get_post_meta( $product_id, '_color_description', true );
            $color_group              = get_post_meta( $product_id, '_color_group', true );
            $pcl_status_description   = get_post_meta( $product_id, '_pcl_status_description', true );
            $pms_color                = get_post_meta( $product_id, '_pms_color', true );
            $ean                      = get_post_meta( $product_id, '_ean', true ) ?? '';

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

        // Fetch product print data from db based on this product master code
        $api_response_for_print_data = $this->fetch_product_print_data_from_db( $this->master_code );
        ob_start();
        ?>
        <script>
            const data = '<?= $api_response_for_print_data ?>';
            const printResponse = JSON.parse(data);
            document.addEventListener("alpine:init", () => {

                Alpine.data("quantityChecker", () => ({
                    quantity: null,
                    get hasQty() {
                        return this.quantity && this.quantity > 0;
                    }
                })),

                    Alpine.data("printData", () => ({

                        printData: printResponse,
                        cachedSelectedPrintData: [],
                        selectedPrintData: [],
                        productPrice: null,
                        quantityFieldValue: null,

                        // Function to add data only if it doesn't already exist in selectedPrintData
                        addData(item, maxColors, selectedTechniqueId) {
                            // Check item already exists
                            let itemExists = this.findCachedData(item);
                            // If item exists change it's maxColors and replace it. If not, add it
                            if (itemExists) {
                                let index = this.cachedSelectedPrintData.indexOf(itemExists);
                                itemExists.maxColors = maxColors;
                                itemExists.selectedTechniqueId = selectedTechniqueId;
                                this.cachedSelectedPrintData.splice(index, 1, itemExists);
                                return;
                            }
                            this.cachedSelectedPrintData.push({
                                ...item,
                                maxColors: maxColors,
                                selectedTechniqueId: selectedTechniqueId
                            });
                        },
                        isTechniqueSelected(item, technique) {
                            return this.findCachedData(item)?.selectedTechniqueId == technique.id;
                        },

                        findCachedData(item) {
                            // Check item already exists
                            return this.cachedSelectedPrintData.filter(selectedItem => selectedItem.position_id === item.position_id).pop();
                        },
                        findSelectedData(item) {
                            // Check item already exists
                            return this.selectedPrintData.filter(selectedItem => selectedItem.position_id === item.position_id).pop();
                        },

                        addCachedData() {
                            // Add unique items to selectedPrintData
                            this.selectedPrintData = [...this.cachedSelectedPrintData];
                        },

                        removeData(item) {
                            this.removeCachedData(this.findCachedData(item));
                            this.removeSelectedData(this.findSelectedData(item));
                        },
                        removeCachedData(item) {
                            let index = this.cachedSelectedPrintData.indexOf(item);
                            if (index > -1) {
                                this.cachedSelectedPrintData.splice(index, 1);
                            }
                        },
                        removeSelectedData(item) {
                            let index = this.selectedPrintData.indexOf(item);
                            if (index > -1) {
                                this.selectedPrintData.splice(index, 1);
                            }
                        },

                        coverImage(item) {
                            return item.images[0].print_position_image_with_area;
                        },

                        techniques(item) {
                            return item.printing_techniques;
                        },

                        isItemSelected(item) {
                            // Check if item with the same position_id already exists in selectedPrintData
                            return this.findSelectedData(item);
                        },

                        priceCalculation(quantity, price) {
                            this.quantityFieldValue = quantity;
                            let calculation = (quantity * price);
                            this.productPrice = calculation;
                        },
                        logSelectedData() {
                            console.log(this.selectedPrintData);
                        },
                        savePrintPositionsDataToCookie(positions) {
                            // Convert the positions object/array to a JSON string
                            const jsonData = JSON.stringify(positions);
                            // Product id
                            const productId = '<?= $this->product_id ?>';

                            // Set the cookie with the key 'printing_positions' and the JSON data
                            document.cookie = `printing_positions_${productId}=${jsonData}; path=/; max-age=${60 * 60 * 24};`; // 24 hours expiration
                        }
                    }));

            });
        </script>
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

            <div class="product-configurator-body" x-data="quantityChecker">
                <div class="row" x-data="printData">
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
                                                        <div style="height: 35px; width: 35px; background: <?php echo strtolower( $this->color_group ); ?>; border: 1px solid black;"
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
                                                    <input type="number"
                                                        @keyup="priceCalculation($el.value, <?php echo $this->product_price; ?>)"
                                                        placeholder="0" class="input-quantity" x-model="quantity" />
                                                </div>
                                            </div>
                                            <div class="col-sm-12 mt-1">
                                                <div class="row justify-content-around align-items-center">
                                                    <div class="col-4">
                                                        <div class="stock-data">
                                                            <div><span class="stock-value">
                                                                    <?php echo $this->product_stock; ?></span> En Stock</div>
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
                                            <div class="col-sm-6 text-end close-button">
                                                <!-- <i class="fa-solid fa-xmark"></i> -->
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /Remove color row button -->
                                </div>
                            </div>
                        </div>
                        <!-- Add more color button -->
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
                                        <template x-for="(item, index) in selectedPrintData">
                                            <div class="print-position pb-2 ms-3" data-name="FRONT">
                                                <div class="technique-wrapper row align-items-center justify-content-evenly"
                                                    data-technique-code="T1">
                                                    <div class="thumb-wrapper col-sm-4 border-right me-2">
                                                        <div class="row align-items-center">
                                                            <div class="thumb-image col-4">
                                                                <img class="thumb" :src="coverImage(item)">
                                                            </div>
                                                            <div class="position-info col-8">
                                                                <div class="position-name">
                                                                    <span class="position-name-serial"
                                                                        x-text="++index + '. ' + item.position_id"></span>
                                                                </div>
                                                                <div class="position-infos">
                                                                    <span>Transfer serigráfico</span>
                                                                    <span>Colores máximos : <span class="color-count"
                                                                            x-text="item.maxColors"></span></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="size-wrapper col">
                                                        <div class="row align-items-center justify-content-between">
                                                            <div class="col bg-white border-right">
                                                                <span class="input-title"
                                                                    x-text="`Ancho (${item.print_size_unit})`"></span>
                                                                <input type="number" class="print-position-number" size="7" min="0"
                                                                    :value="item.max_print_size_height">
                                                            </div>
                                                            <div class="col bg-white">
                                                                <span class="input-title"
                                                                    x-text="`Alto (${item.print_size_unit})`"></span>
                                                                <input type="number" class="print-position-number" size="7" min="0"
                                                                    :value="item.max_print_size_width">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="colors-wrapper col-sm-2">
                                                        <select name="" id="">
                                                            <option :value="item.maxColors" x-text="`${item.maxColors} Colors`">
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="remove-wrapper col-sm-2">
                                                        <div class="remove-button cursor-pointer" @click="removeData(item)">
                                                            <i class="fa-solid fa-xmark"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                        <!-- /print position REPEAT:-->
                                    </div>
                                    <!-- /print positions -->
                                </div>
                                <!-- Add print position button -->
                                <div class="add-print-position-button-portion">
                                    <div class="mt-3">
                                        <button :class="hasQty ? '' : 'disabled'" id="add-print-position-button" data-toggle="modal"
                                            data-target="#add_print_position_modal_button" x-ref="addPositionButton"
                                            :disabled="!hasQty">
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
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </div>
                                            <!-- Modal body -->
                                            <div class="modal-body">
                                                <div class="row">
                                                    <template x-for="(item, index) in printData.printing_positions">
                                                        <div :class="!isItemSelected(item) ? '' : 'disabled'"
                                                            class="col-sm-3 border-right mb-3 modal-inner-single-item position-relative">
                                                            <!-- Modal Header: Position title and dimensions -->
                                                            <div class="modal-item-header">
                                                                <span class="modal-item-title d-block" x-text="item.position_id">
                                                                </span>
                                                                <span class="modal-item-dimensions d-block"
                                                                    x-text="`${item['max_print_size_height']} ${item['print_size_unit']} x  ${item['max_print_size_width']} ${item['print_size_unit']}`">
                                                                </span>
                                                            </div>
                                                            <!-- Modal Image: Display the print position image -->
                                                            <div class="modal-item-image">
                                                                <img :src="coverImage(item)" alt="example product image"
                                                                    width="120px" style="display: block; margin: 1.5rem auto;">
                                                            </div>
                                                            <!-- Modal Radio Buttons: -->
                                                            <div class="modal-item-radios">
                                                                <template x-for="(technique, tindex) in techniques(item)">
                                                                    <div class="modal-item-radio"
                                                                        :data-printing-technique-id="technique.id">
                                                                        <div
                                                                            class="d-flex align-items-center justify-content-between">
                                                                            <label class="m-0 cursor-pointer"
                                                                                :class="{'be-selected': isTechniqueSelected(item, technique)}">
                                                                                <!-- Radio input for printing technique -->
                                                                                <input
                                                                                    @change="$el.value && addData(item, technique.max_colours, technique.id)"
                                                                                    type="radio" class="m-0"
                                                                                    :disabled="isItemSelected(item)"
                                                                                    :name="`print_data_${index}_technique`"
                                                                                    :value="technique.id"
                                                                                    :checked="isTechniqueSelected(item, technique)">
                                                                                <!-- Static Label for the printing technique radio input -->
                                                                                Transfer
                                                                                serigráfico

                                                                                <span class="modal-item-color-count">Colores máximos
                                                                                    : <span class="color-count"
                                                                                        x-text="technique.max_colours"></span></span>
                                                                            </label>
                                                                        </div>
                                                                        <!-- Display the maximum number of colors for the technique -->
                                                                    </div>
                                                                </template>
                                                            </div>

                                                            <!-- Clear Selection Button: -->
                                                            <div class="modal-item-clear">
                                                                <button type="button" class="modal-item-clear-button w-100"
                                                                    @click="removeData(item)"><?php esc_html_e( 'Borrar selección', 'bulk-product-import' ); ?></button>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                            <!-- /Modal body -->
                                            <div class="modal-footer">
                                                <button type="button" class="modal-close-button"
                                                    data-dismiss="modal"><?php esc_html_e( 'Cancelar', 'bulk-product-import' ) ?></button>
                                                <button type="button" class="modal-save-button"
                                                    @click="addCachedData"><?php esc_html_e( 'Añadir', 'bulk-product-import' ) ?></button>
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
                                <div class="value"></div>
                            </div>
                            <div class="summary-row product-price">
                                <div class="text" data-default="Precio artículo">
                                    <?php esc_html_e( 'Precio artículo', 'bulk-product-import' ) ?>
                                    <!-- <span>(cantidad: <span x-text="quantityFieldValue"></span> )</span> -->
                                    <span x-text="quantityFieldValue ? `(cantidad: ${quantityFieldValue})` : ''"></span>
                                </div>
                                <div class="value"
                                    x-text="productPrice ? `${productPrice} <?php echo $this->currency_symbol; ?>` : '-'"></div>
                            </div>
                            <div class="grand-totals underline">
                                <div class="summary-row grand-total">
                                    <div><?php esc_html_e( 'Total (incl. transporte)', 'bulk-product-import' ) ?></div>
                                    <div class="total"
                                        x-text="productPrice ? `${productPrice} <?php echo $this->currency_symbol; ?>` : '-'">
                                    </div>
                                </div>
                                <div class="summary-row price-per-item">
                                    <div class="price-per-item-subheading">
                                        <?php esc_html_e( 'Precio por artículo', 'bulk-product-import' ) ?>
                                    </div>
                                    <div class="value">-</div>
                                </div>
                            </div>
                        </div>
                        <div class="personalize-button">
                            <button class="be-add-to-cart-btn w-100 d-flex align-items-center justify-content-between p-3"
                                data-toggle="modal" data-target="#customMediaArtwork">
                                <span class="button-text"><?php esc_html_e( 'Personalizar', 'bulk-product-import' ) ?></span>
                                <span><i class="fa-solid fa-arrow-right"></i></span>
                            </button>
                            <!-- Custom media, artwork and design -->
                            <!-- Modal -->
                            <div class="modal fade" id="customMediaArtwork" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Custom Media</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            ...
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="modal-close-button" data-dismiss="modal">Close</button>
                                            <button type="button" class="modal-save-button">Save changes</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Custom media, artwork and design -->
                            <button :class="hasQty ? '' : 'd-none'" @click="savePrintPositionsDataToCookie(selectedPrintData)"
                                class="be-add-to-cart-btn-without-configure w-100 d-flex align-items-center justify-content-between mt-2 p-3"
                                data-product-id="<?php echo $this->product_id; ?>">
                                <span
                                    class="button-text"><?php esc_html_e( 'Añadir al carrito sin marcaje', 'bulk-product-import' ) ?>
                                </span>
                                <span class="add-to-cart-loader"></span>
                                <span><img class="check-icon d-none" src="" alt=""></span>
                                <span><i class="fa-solid fa-arrow-right"></i></span>
                            </button>
                            <div class="view-cart-container mt-3 d-none">
                                <a class="view-cart-url text-white text-capitalize" href="">
                                    <?php esc_html_e( 'Ver carrito', 'bulk-product-import' ) ?></a>
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
        return $print_data;
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

    public function custom_add_to_cart_handler() {
        try {
            // Get product values
            $product_id = intval( $_POST['product_id'] ) ?? 0;
            $quantity   = intval( $_POST['quantity'] ) ?? 0;

            // Validate the product exists and can be added to cart
            $product = wc_get_product( $product_id );
            if ( !$product ) {
                throw new Exception( 'Invalid product ID' );
            }

            // Get product quantity
            $product_quantity = $product->get_stock_quantity();
            if ( $product_quantity < $quantity ) {
                throw new Exception( 'Insufficient product quantity' );
            }

            // Add to cart
            $cart_item_key = WC()->cart->add_to_cart( $product_id, $quantity );

            // Prepare success response
            $success_response = [
                'success'        => true,
                'message'        => 'Product added to cart',
                'cart_page_url'  => wc_get_cart_url(),
                'check_icon_url' => BULK_PRODUCT_IMPORT_PLUGIN_URL . '/assets/images/check.png',
            ];

            // Check if product was successfully added to cart
            if ( $cart_item_key ) {
                wp_send_json_success( $success_response );
            } else {
                throw new Exception( 'Failed to add product to cart' );
            }

        } catch (Exception $e) {
            // Handle exceptions and send error response
            wp_send_json_error( [ 'success' => false, 'message' => $e->getMessage() ] );
        }

        wp_die(); // Required for WordPress AJAX
    }

}