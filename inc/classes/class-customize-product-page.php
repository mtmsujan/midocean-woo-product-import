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
    private $technique_labels;

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
        add_action( 'wp_ajax_upload_files', [ $this, 'handle_file_upload' ] );
        add_action( 'wp_ajax_nopriv_upload_files', [ $this, 'handle_file_upload' ] );
        add_action( 'wp_ajax_get_technique_label', [ $this, 'get_technique_label' ] );
        add_action( 'wp_ajax_nopriv_get_technique_label', [ $this, 'get_technique_label' ] );

        // get technique labels
        $file                   = BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/inc/files/labels.json';
        $this->technique_labels = file_get_contents( $file );

        // put to log
        // $this->put_program_logs( $this->technique_labels );
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
        $print_data_array            = json_decode( $api_response_for_print_data, true );

        $manipulation_cost_file = BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/inc/files/manipulation-cost.json';
        $manipulation_cost      = file_get_contents( $manipulation_cost_file );

        // Initialize an empty array to store the printing technique ids
        $printing_technique_ids = array();

        // Extract the printing positions
        $printing_positions = $print_data_array['printing_positions'];
        $print_manipulation = $print_data_array['print_manipulation'];

        // Loop through the printing positions and extract printing technique ids
        foreach ( $printing_positions as $position ) {
            if ( isset( $position['printing_techniques'] ) ) {
                foreach ( $position['printing_techniques'] as $technique ) {
                    $printing_technique_ids[] = $technique['id'];
                }
            }
        }

        // Initialize the array to store print price data for each technique
        $product_print_price_data = [];

        // Loop through each printing technique id to fetch print price data
        foreach ( $printing_technique_ids as $technique_id ) {

            // Get the print price data from the database based on the technique id
            $single_print_data = $this->get_print_price_data_from_db( $technique_id );

            // Put print price data in logs
            // $this->put_program_logs( 'Single Print Data: ' . json_encode( $single_print_data ) );

            // Assuming $single_print_data is an array, store it in the required format
            if ( !empty( $single_print_data ) ) {

                // Extract the print price data
                $setup_price        = $single_print_data['setup_price'];
                $setup_repeat_price = $single_print_data['setup_repeat_price'];
                $var_costs          = $single_print_data['var_cost'];

                // Transform to required format
                $product_print_price_data[$technique_id] = [
                    'technique_id'       => $technique_id,
                    'setup_price'        => $setup_price,
                    'setup_repeat_price' => $setup_repeat_price,
                    'var_cost'           => $var_costs,
                ];
            }
        }

        // Encode to json
        $product_print_price_data = json_encode( $product_print_price_data );

        // Put print price data in logs
        // $this->put_program_logs( 'Print price Data: ' . $product_print_price_data );

        // put product print data in logs
        // $this->put_program_logs( 'Print data response: ' . $api_response_for_print_data );

        ob_start();
        ?>
        <script>
            const data = '<?= $api_response_for_print_data ?>';
            const printResponse = JSON.parse(data);
            const labels = '<?= $this->technique_labels ?>';
            const technique_labels = JSON.parse(labels);
            const printPriceData = '<?= $product_print_price_data ?>';
            const manipulationCost = '<?= $manipulation_cost ?>';
            const printManipulationId = '<?= $print_manipulation ?>';
            document.addEventListener("alpine:init", () => {

                Alpine.data("quantityChecker", () => ({
                    quantity: null,
                    get hasQty() {
                        return this.quantity && this.quantity > 0;
                    }
                })),

                    Alpine.data("printData", () => ({

                        printData: printResponse,
                        technique_labels: technique_labels,
                        printPriceData: JSON.parse(printPriceData),
                        manipulationCost: JSON.parse(manipulationCost),
                        printManipulationId: printManipulationId,
                        cachedSelectedPrintData: [],
                        selectedPrintData: [],
                        quantityFieldValue: 0,
                        artworkName: null,
                        mockupName: null,
                        instructions: null,
                        customPrintMedias: null,
                        showAlertMessage: false,
                        showPrintPriceCalculation: false,
                        shippingCost: 8, // Replace: with actual shipping cost
                        costManipulation: 0,
                        printingPositionCost: 0,
                        totalNormalPriceWithoutShipping: 0,
                        totalNormalPriceWithShipping: 0,
                        totalPrintingPrice: 0,
                        totalPriceWithPrintingCost: 0,

                        init() {
                            this.$watch('selectedPrintData', (newValue) => {
                                if (newValue.length > 0) {
                                    this.showAlertMessage = false;
                                    this.showPrintPriceCalculation = true;
                                    this.calculateTotalPriceWithPrintingCost();
                                } else {
                                    this.showPrintPriceCalculation = false;
                                }
                            });
                        },

                        // return technique label by technique id
                        getTechniqueLabel(techniqueId) {
                            return this.technique_labels[techniqueId].label;
                        },

                        getPrintPriceData(techniqueId) {
                            return this.printPriceData[techniqueId];
                        },

                        getSetupCost(techniqueId) {
                            return this.printPriceData[techniqueId].setup_price;
                        },

                        getSetupRepeatCost(techniqueId) {
                            return this.printPriceData[techniqueId].setup_repeat_price;
                        },

                        getManipulationCost(id) {
                            return this.manipulationCost[id].price;
                        },

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

                            this.addCachedData(); // Add cached data to selectedPrintData
                            this.calculateTotalPrintingCost(); // Recalculate the total cost
                        },

                        // Function to calculate total printing cost
                        calculateTotalPrintingCost() {
                            let totalCost = 0;
                            // Iterate over selectedPrintData and calculate the cost for each position
                            this.selectedPrintData.forEach(item => {
                                const techniqueId = item.selectedTechniqueId;
                                if (techniqueId) {
                                    totalCost += this.priceCalculationWithPrintingCostForSingleItem(techniqueId, this.quantityFieldValue);
                                }
                            });
                            this.totalPrintingPrice = totalCost.toFixed(2); // Update the state with the total cost
                        },

                        calculateTotalPriceWithPrintingCost() {
                            /**
                             * Calculate total price with printing cost
                             * Formula = this.totalNormalPriceWithShipping + this.totalPrintingPrice + this.costManipulation
                             */
                            this.totalPriceWithPrintingCost = parseFloat(this.totalNormalPriceWithShipping) + parseFloat(this.totalPrintingPrice) + parseFloat(this.costManipulation);
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
                            this.calculateTotalPrintingCost(); // Recalculate the total cost after removal
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

                        // Product normal price calculation with shipping cost
                        priceCalculationWithoutPrintingCost(quantity, price) {

                            // update quantity value to this.quantityFieldValue state
                            this.quantityFieldValue = quantity;

                            /**
                             * Simple price calculation
                             * Formula = quantity * price
                             * Price is product actual price
                             */
                            let simpleCalculation = (quantity * price).toFixed(2);
                            // update this.totalNormalPriceWithoutShipping state
                            this.totalNormalPriceWithoutShipping = simpleCalculation;

                            // calculate if totalNormalPriceWithoutShipping value >0
                            if (this.totalNormalPriceWithoutShipping > 0) {
                                this.totalNormalPriceWithShipping = this.shippingCost + parseFloat(this.totalNormalPriceWithoutShipping);
                            } else {
                                this.totalNormalPriceWithShipping = 0;
                            }

                            // Save to cookie this.totalNormalPriceWithShipping value for 1 hour, key is _calculated_price
                            this.setCookie("_calculated_price", this.totalNormalPriceWithShipping, 1); // 1 hour
                        },

                        setCookie(name, value, hours) {
                            // Get the current date
                            let date = new Date();
                            // Set the expiration date
                            date.setTime(date.getTime() + (hours * 60 * 60 * 1000));
                            //calculate the expires date
                            let expires = "expires=" + date.toUTCString();
                            // set the cookie
                            document.cookie = name + "=" + value + ";" + expires + ";path=/";
                        },

                        priceCalculationWithPrintingCostForSingleItem(techniqueId, quantity) {

                            // Get the manipulation cost and calculate the total print cost
                            const manipulationCost = this.getManipulationCost(this.printManipulationId);
                            this.costManipulation = quantity * manipulationCost;

                            // Get the print price data for the given techniqueId
                            const printPriceData = this.getPrintPriceData(techniqueId);
                            // Get the setup cost for the given techniqueId
                            const setupCost = parseFloat(this.getSetupCost(techniqueId).replace(",", "."));
                            // Get the setup repeat cost for the given techniqueId
                            const setupRepeatCost = parseFloat(this.getSetupRepeatCost(techniqueId).replace(",", "."));

                            // Initialize the total print cost
                            let price = 0;

                            // Loop through var_cost ranges to find the correct one
                            for (let range of printPriceData.var_cost) {
                                const areaFrom = parseFloat(range.area_from);
                                const areaTo = parseFloat(range.area_to);

                                // Assuming quantity falls within a certain range
                                if (quantity >= areaFrom && quantity <= areaTo) {
                                    // Find the correct price based on minimum_quantity
                                    for (let scale of range.scales) {
                                        if (quantity >= parseFloat(scale.minimum_quantity)) {
                                            price = parseFloat(scale.price.replace(",", "."));
                                        }
                                    }
                                }
                            }

                            /**
                             * Calculate total printing cost for single printing position
                             * Formula = setup_cost + (quantity * price)
                             */
                            const printingCostForSingleItem = setupCost + (quantity * price);

                            return printingCostForSingleItem;
                        },

                        savePrintPositionsDataToCookie() {

                            // Combine selectedPrintData (array) and customPrintMedias (object)
                            const combinedData = {
                                selectedPrintData: this.selectedPrintData,
                                customPrintMedias: this.customPrintMedias
                            };

                            // Convert the positions object/array to a JSON string
                            const jsonData = JSON.stringify(combinedData);

                            // Product id
                            const productId = '<?= $this->product_id ?>';

                            // Set the cookie with the key 'printing_positions' and the JSON data
                            document.cookie = `printing_positions_${productId}=${jsonData}; path=/; max-age=${60 * 60 * 24};`; // 24 hours expiration
                        },

                        // File Upload Handler for artwork and mockup
                        submitForm() {

                            // Get upload media loader
                            let uploadMediaLoaderArea = document.querySelector(".upload-media-loader");
                            // Remove d-none class from loading area
                            uploadMediaLoaderArea.classList.remove("d-none");
                            // Add loader class to loading area
                            uploadMediaLoaderArea.classList.add("loader");

                            const artworkFile = document.getElementById('upload-artwork').files[0];
                            const mockupFile = document.getElementById('upload-mockup').files[0];
                            const maxFileSize = 15 * 1024 * 1024; // 15 MB in bytes
                            const allowedFileTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg',
                                'application/pdf', 'image/svg+xml', 'image/x-icon',
                                'image/bmp', 'image/tiff', 'application/postscript',
                                'application/vnd.adobe.photoshop'];

                            // Validate artwork file
                            if (artworkFile) {
                                if (artworkFile.size > maxFileSize) {
                                    alert('Artwork file exceeds the maximum size of 15 MB.');
                                    return;
                                }

                                if (!allowedFileTypes.includes(artworkFile.type)) {
                                    alert('Artwork file type is not allowed. Please upload a .jpg, .png, .gif, .jpeg, .pdf, .svg, .eps, .ico, .bmp, .tif, .ai, or .psd file.');
                                    return;
                                }
                            }

                            // Validate mockup file
                            if (mockupFile) {
                                if (mockupFile.size > maxFileSize) {
                                    alert('Mockup file exceeds the maximum size of 15 MB.');
                                    return;
                                }

                                if (!allowedFileTypes.includes(mockupFile.type)) {
                                    alert('Mockup file type is not allowed. Please upload a .jpg, .png, .gif, .jpeg, .pdf, .svg, .eps, .ico, .bmp, .tif, .ai, or .psd file.');
                                    return;
                                }
                            }

                            // Prepare formData if validation passes
                            const formData = new FormData();
                            formData.append('artwork', artworkFile);
                            formData.append('mockup', mockupFile);
                            formData.append('instructions', this.instructions);
                            formData.append('action', 'upload_files');
                            formData.append('security', '<?= wp_create_nonce( 'upload_files_nonce' ); ?>');

                            // Send AJAX request via fetch
                            fetch('<?= admin_url( 'admin-ajax.php' ); ?>', {
                                method: 'POST',
                                body: formData,
                            })
                                .then(response => response.json())
                                .then(result => {
                                    if (result.success) {
                                        this.customPrintMedias = result.data;
                                        // Remove d-none class from loading area
                                        uploadMediaLoaderArea.classList.remove("loader");
                                        uploadMediaLoaderArea.innerHTML = '<i class="fa-solid fa-check"></i>';
                                    } else {
                                        console.error(result.data.message);
                                    }
                                })
                                .catch(error => console.error('Error:', error));
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
                                                        @keyup="priceCalculationWithoutPrintingCost($el.value, <?php echo $this->product_price; ?>)"
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
                                                                    <span
                                                                        x-text="getTechniqueLabel(item.selectedTechniqueId)"></span>
                                                                    <br>
                                                                    <span class="color-count"
                                                                        x-text="item.maxColors == 0 ? 'A todo color' : `Colores máximos: ${item.maxColors}`"></span>
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
                                                                    :value="item.max_print_size_width">
                                                            </div>
                                                            <div class="col bg-white">
                                                                <span class="input-title"
                                                                    x-text="`Alto (${item.print_size_unit})`"></span>
                                                                <input type="number" class="print-position-number" size="7" min="0"
                                                                    :value="item.max_print_size_height">
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
                                                                    x-text="`${item['max_print_size_width']} ${item['print_size_unit']} x  ${item['max_print_size_height']} ${item['print_size_unit']}`">
                                                                </span>
                                                            </div>
                                                            <!-- Modal Image: Display the print position image -->
                                                            <div class="modal-item-image">
                                                                <img :src="coverImage(item)" alt="example product image"
                                                                    style="display: block; margin: 1.5rem auto; height: 120px !important">
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

                                                                                <!-- Dynamically fetched label -->
                                                                                <span
                                                                                    x-text="getTechniqueLabel(technique.id)"></span>

                                                                                <span class="modal-item-color-count"
                                                                                    x-text="technique.max_colours == 0 ? 'A todo color' : `Colores máximos: ${technique.max_colours}`"></span>
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
                                <img src="<?= $this->product_small_image; ?>" alt="<?= $this->product_name; ?> product photo"
                                    height="75" width="75">
                                <div class="name-wrapper">
                                    <span class="main-text"><?= $this->product_number; ?></span>
                                    <span class="sub-text"><?= $this->product_name; ?></span>
                                </div>
                            </div>

                            <div x-show="showPrintPriceCalculation">

                                <template x-for="(item, index) in selectedPrintData">
                                    <div class="summary-row underline printing-position-cost">
                                        <div>
                                            <span x-text="`Posición de impresión ${index + 1}:`"></span>
                                            <span
                                                x-text="item.maxColors == 0 ? 'A todo color' : `${item.maxColors} color`"></span>
                                        </div>
                                        <div class="value">
                                            <!-- printing position cost here -->
                                            <span
                                                x-text="priceCalculationWithPrintingCostForSingleItem(item.selectedTechniqueId ? item.selectedTechniqueId : '', quantityFieldValue)"></span>
                                        </div>
                                    </div>
                                </template>

                                <div class="summary-row underline const-manipulation">
                                    <div><?php esc_html_e( 'Coste manipulación', 'bulk-product-import' ) ?>
                                    </div>
                                    <div class="value">
                                        <!-- cost manipulation here -->
                                        <span x-text="costManipulation ? `${costManipulation.toFixed(2)}` : '-'"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="summary-row underline shipping">
                                <div><?php esc_html_e( 'Portes web península (oficina 20 eur)', 'bulk-product-import' ) ?></div>
                                <div class="value">
                                    <!-- Shipping cost here -->
                                    <span
                                        x-text="shippingCost && totalNormalPriceWithoutShipping > 0 ? `${shippingCost.toFixed(2)}` : '-'"></span>
                                </div>
                            </div>
                            <div class="summary-row product-price">
                                <div class="text" data-default="Precio artículo">
                                    <?php esc_html_e( 'Precio artículo', 'bulk-product-import' ) ?>
                                    <span x-text="quantityFieldValue ? `(cantidad: ${quantityFieldValue})` : ''"></span>
                                </div>
                                <div class="value"
                                    x-text="totalNormalPriceWithoutShipping > 0 ? `${totalNormalPriceWithoutShipping} <?= $this->currency_symbol; ?>` : '-'">
                                </div>
                            </div>
                            <div class="grand-totals underline">
                                <div class="summary-row grand-total">
                                    <div><?php esc_html_e( 'Total (incl. transporte)', 'bulk-product-import' ) ?></div>
                                    <div class="total"
                                        x-text="totalNormalPriceWithShipping ? `${totalNormalPriceWithShipping.toFixed(2)} <?= $this->currency_symbol; ?>` : '-'">
                                    </div>
                                </div>
                                <div class="summary-row price-per-item">
                                    <div class="price-per-item-subheading">
                                        <?php esc_html_e( 'Precio por artículo', 'bulk-product-import' ) ?>
                                    </div>
                                    <div class="value">
                                        <span x-text="totalPrintingPrice > 0 ? `${totalPriceWithPrintingCost.toFixed(2)} <?= $this->currency_symbol; ?>` : `${totalNormalPriceWithShipping.toFixed(2)} <?= $this->currency_symbol; ?>`"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="personalize-button">
                            <button @click="if (!(hasQty && selectedPrintData.length > 0)) { showAlertMessage = true; }"
                                :class="hasQty && selectedPrintData.length > 0 ? '' : 'be-disabled'"
                                class="be-add-to-cart-btn w-100 d-flex align-items-center justify-content-between p-3"
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
                                            <h5 class="customize-modal-title" id="exampleModalLongTitle">
                                                <?php esc_html_e( 'Personalizar', 'bulk-product-import' ) ?>
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="validation-instruction-area mb-3">
                                                <p class="mb-0 validation-instruction">
                                                    <?php esc_html_e( 'Tamaño máximo del archivo 15 MB. Sólo archivos .jpg, .png, .gif, .jpeg, .pdf, .svg, .eps, .ico, .bmp, .tif, .ai, .psd.', 'bulk-product-import' ) ?>
                                                </p>
                                            </div>
                                            <div class="artwork-area mb-3">
                                                <div class="row align-items-center">
                                                    <div class="col-sm-4">
                                                        <label class="file-upload-button">
                                                            <?php esc_html_e( 'Subir obra de arte', 'bulk-product-import' ) ?>
                                                            <input type="file" name="upload-artwork" id="upload-artwork"
                                                                @change="artworkName = $event.target.files[0]?.name" hidden>
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <span class="upload-artwork-url-preview" x-text="artworkName"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mockup-area mb-3">
                                                <div class="row align-items-center">
                                                    <div class="col-sm-4">
                                                        <label class="file-upload-button">
                                                            <?php esc_html_e( 'Subir maqueta', 'bulk-product-import' ) ?>
                                                            <input type="file" name="upload-mockup" id="upload-mockup"
                                                                @change="mockupName = $event.target.files[0]?.name" hidden>
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <span class="upload-mockup-url-preview" x-text="mockupName"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="instructions-area">
                                                <label
                                                    class="instructions-form-label"><?php esc_html_e( 'Instrucciones', 'bulk-product-import' ) ?></label>
                                                <textarea class="instructions-textarea" name="instructions" id="instructions"
                                                    cols="30" rows="5" x-model="instructions"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="modal-close-button"
                                                data-dismiss="modal"><?php esc_html_e( 'Cancelar', 'bulk-product-import' ) ?>
                                            </button>
                                            <button type="button" class="modal-save-button" id="customize-modal-save-button"
                                                @click="submitForm">
                                                <?php esc_html_e( 'Añadir', 'bulk-product-import' ) ?>
                                                <span class="upload-media-loader d-none ms-2"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Custom media, artwork and design -->
                            <button :class="hasQty ? '' : 'd-none'" @click="savePrintPositionsDataToCookie"
                                class="be-add-to-cart-btn-without-configure w-100 d-flex align-items-center justify-content-between mt-2 p-3"
                                data-product-id="<?php echo $this->product_id; ?>">
                                <span class="button-text"><?php esc_html_e( 'Añadir a la cesta', 'bulk-product-import' ) ?>
                                </span>
                                <span class="add-to-cart-loader"></span>
                                <span><img class="check-icon d-none" src="" alt=""></span>
                                <span><i class="fa-solid fa-arrow-right"></i></span>
                            </button>
                            <div class="view-cart-container mt-3 d-none">
                                <a class="view-cart-url text-white text-capitalize" href="">
                                    <?php esc_html_e( 'Ver carrito', 'bulk-product-import' ) ?></a>
                            </div>
                            <!-- Alert message -->
                            <div class="alert mt-3 be-alert-message" role="alert" x-show="showAlertMessage" x-transition
                                x-cloak>
                                <div class="row align-items-center justify-content-start">
                                    <div class="col-sm-2 pe-0">
                                        <i class="fa-solid fa-circle-minus me-2" style="color: #ff3d3d; font-size:14px;"></i>
                                    </div>
                                    <div class="col-sm-10 ps-0">
                                        <span
                                            class="alert-message-text"><?php esc_html_e( 'Por favor, configura al menos una posición de impresión.', 'bulk-product-import' ) ?></span>
                                    </div>
                                </div>
                            </div>
                            <!-- /Alert message -->
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

    public function handle_file_upload() {

        // Make sure load the file required to handle file uploads
        if ( !function_exists( 'media_handle_upload' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            require_once( ABSPATH . 'wp-admin/includes/media.php' );
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
        }

        // Check for nonce security
        check_ajax_referer( 'upload_files_nonce', 'security' );

        $response = [];

        // Handle artwork file upload
        if ( !empty( $_FILES['artwork'] ) ) {
            $artwork_id = media_handle_upload( 'artwork', 0 );
            if ( !is_wp_error( $artwork_id ) ) {
                $response['artwork_url'] = wp_get_attachment_url( $artwork_id );
            } else {
                wp_send_json_error( [ 'message' => 'Error uploading artwork.' ] );
            }
        }

        // Handle mockup file upload
        if ( !empty( $_FILES['mockup'] ) ) {
            $mockup_id = media_handle_upload( 'mockup', 0 );
            if ( !is_wp_error( $mockup_id ) ) {
                $response['mockup_url'] = wp_get_attachment_url( $mockup_id );
            } else {
                wp_send_json_error( [ 'message' => 'Error uploading mockup.' ] );
            }
        }

        // Handle instructions
        if ( !empty( $_POST['instructions'] ) ) {
            $response['instructions'] = sanitize_text_field( $_POST['instructions'] );
        }

        wp_send_json_success( $response );
    }

    function get_technique_label() {
        if ( isset( $_POST['technique_id'] ) ) {
            $technique_id = intval( $_POST['technique_id'] );
            $label        = $this->get_technique_label_from_db( $technique_id );
            wp_send_json_success( [ 'label' => $label ] );
        } else {
            wp_send_json_error( 'No technique ID provided' );
        }
    }

    private function get_technique_label_from_db( $technique_id ) {

        global $wpdb;

        // get table prefix
        $table_prefix = get_option( 'be-table-prefix' ) ?? '';
        $table_name   = $wpdb->prefix . $table_prefix . 'sync_products_print_data_labels';

        // SQL Query
        $sql = "SELECT label_cs FROM $table_name WHERE id = '{$technique_id}'";
        // Execute query
        $technique_label = $wpdb->get_results( $wpdb->prepare( $sql ) );

        // Return technique label
        return $technique_label[0]->label_cs;
    }

    private function get_print_price_data_from_db( $technique_id ) {
        global $wpdb;

        // get table prefix
        $table_prefix = get_option( 'be-table-prefix' ) ?? '';
        $table_name   = $wpdb->prefix . $table_prefix . 'sync_print_price';

        // SQL Query
        $sql = "SELECT technique_id, setup_price, setup_repeat_price, var_cost FROM $table_name WHERE technique_id = '{$technique_id}'";
        // Execute query
        $result = $wpdb->get_results( $wpdb->prepare( $sql ) );

        $result = $result[0];

        $_technique_id      = $result->technique_id;
        $setup_price        = $result->setup_price;
        $setup_repeat_price = $result->setup_repeat_price;
        $var_cost           = $result->var_cost;
        $var_cost           = json_decode( $var_cost, true );

        // initial final result
        $final_result = [
            'technique_id'       => $_technique_id,
            'setup_price'        => $setup_price,
            'setup_repeat_price' => $setup_repeat_price,
            'var_cost'           => $var_cost,
        ];

        // Return technique label
        return $final_result;
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