<?php

namespace BULK_IMPORT\Inc;

use BULK_IMPORT\Inc\Traits\Singleton;

class Create_Order {

    use Singleton;

    public function __construct() {
        // Setup hooks when the class is initialized
        $this->setup_hooks();
    }

    public function setup_hooks() {
        // Add a WooCommerce action hook that triggers on the thank you page
        add_action( 'woocommerce_thankyou', [ $this, 'create_order' ] );
        add_action( 'woocommerce_thankyou', [ $this, 'display_printing_positions_on_thank_you_page' ], 20, 1 );
        add_action( 'woocommerce_order_details_after_order_table', [ $this, 'display_printing_positions_on_order_page' ], 20, 1 );
        add_action( 'woocommerce_before_calculate_totals', [ $this, 'update_cart_item_price' ], 10, 1 );
    }

    function update_cart_item_price( $cart ) {
        // Ensure that the function is not triggered multiple times
        if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) {
            return;
        }

        // Loop through all cart items
        foreach ( $cart->get_cart() as $cart_item ) {
            // Get the product ID and other data from the cart item
            $product_id = $cart_item['product_id'];

            // get calculated product price from cookie
            $new_price = 0;

            if ( $_COOKIE['_calculated_price'] ) {
                $new_price = $_COOKIE['_calculated_price'];
            }

            // Update the cart item price with the new price
            $cart_item['data']->set_price( $new_price );
        }
    }

    public function create_order( $order_id ) {
        // Get order details
        $order = wc_get_order( $order_id );

        // Call the API with the order details
        $api_response = $this->call_api( $order );

        if ( $api_response ) {
            // put api response in logs
            // $this->put_program_logs( 'API Response: ' . $api_response );
            // Decode the API response
            $api_response_decode = json_decode( $api_response, true );
            // Get order type
            $order_type  = $api_response_decode['order_header']['order_type'];
            $order_lines = $api_response_decode['order_lines'];

            // Check if order type is PRINT and get printing positions
            if ( $order_type === 'PRINT' ) {
                $printing_positions = $order_lines[0]['printing_positions']; // Assuming there's at least one order line

                // Prepare an array to store all positions
                $positions_data = array();

                // Loop through printing positions to extract required details
                foreach ( $printing_positions as $position ) {
                    $positions_data[] = array(
                        'id'           => $position['id'],
                        'technique_id' => $position['printing_technique_id'],
                        'artwork_url'  => $position['print_artwork_url'],
                        'mockup_url'   => $position['print_mockup_url'],
                        'instruction'  => $position['print_instruction'],
                    );
                }

                // Save printing positions data in order meta
                update_post_meta( $order_id, '_printing_positions_data', $positions_data );
            }
        }

        // Log the API response if needed
        $this->put_program_logs( 'API Response: ' . $api_response );

        // Handle product and printing positions
        $product_id = null;
        foreach ( $order->get_items() as $item ) {
            $product_id = $item->get_product()->get_id();
            break; // Just considering the first item for product id in this context
        }

        // Cookie key for printing positions
        $cookie_key               = "printing_positions_" . $product_id;
        $pantone_color_cookie_key = "_selectedPantoneColors";
        $selectedPrintData        = "_selectedPrintData";
        $calculatedPrice          = "_calculated_price";
        $max_colors               = "_max_colors";

        // Check and delete the printing positions cookie after the order is complete
        if ( isset( $_COOKIE[$cookie_key] ) ) {
            // Delete the cookie by setting its expiration to the past
            setcookie( $cookie_key, '', time() - 3600, '/' );
        }
        if ( isset( $_COOKIE[$pantone_color_cookie_key] ) ) {
            // Delete the cookie by setting its expiration to the past
            setcookie( $pantone_color_cookie_key, '', time() - 3600, '/' );
        }
        if ( isset( $_COOKIE[$selectedPrintData] ) ) {
            // Delete the cookie by setting its expiration to the past
            setcookie( $selectedPrintData, '', time() - 3600, '/' );
        }
        if ( isset( $_COOKIE[$calculatedPrice] ) ) {
            // Delete the cookie by setting its expiration to the past
            setcookie( $calculatedPrice, '', time() - 3600, '/' );
        }
        if ( isset( $_COOKIE[$max_colors] ) ) {
            // Delete the cookie by setting its expiration to the past
            setcookie( $max_colors, '', time() - 3600, '/' );
        }
    }

    function display_printing_positions_on_thank_you_page( $order_id ) {
        // Retrieve printing positions data from order meta
        $printing_positions_data = get_post_meta( $order_id, '_printing_positions_data', true );

        if ( $printing_positions_data ) {
            echo '<h2>Printing Details</h2>';
            foreach ( $printing_positions_data as $position ) {
                echo '<p><strong>Position:</strong> ' . esc_html( $position['id'] ) . '</p>';
                echo '<p><strong>Technique ID:</strong> ' . esc_html( $position['technique_id'] ) . '</p>';
                echo '<p><strong>Artwork:</strong> <a href="' . esc_url( $position['artwork_url'] ) . '" target="_blank">View Artwork</a></p>';
                echo '<p><strong>Mockup:</strong> <a href="' . esc_url( $position['mockup_url'] ) . '" target="_blank">View Mockup</a></p>';
                echo '<p><strong>Instructions:</strong> ' . esc_html( $position['instruction'] ) . '</p>';
            }
        }
    }

    function display_printing_positions_on_order_page( $order ) {
        // Retrieve printing positions data from order meta
        $printing_positions_data = get_post_meta( $order->get_id(), '_printing_positions_data', true );

        if ( $printing_positions_data ) {
            echo '<h2>Printing Details</h2>';
            foreach ( $printing_positions_data as $position ) {
                echo '<p><strong>Position:</strong> ' . esc_html( $position['id'] ) . '</p>';
                echo '<p><strong>Technique ID:</strong> ' . esc_html( $position['technique_id'] ) . '</p>';
                echo '<p><strong>Artwork:</strong> <a href="' . esc_url( $position['artwork_url'] ) . '" target="_blank">View Artwork</a></p>';
                echo '<p><strong>Mockup:</strong> <a href="' . esc_url( $position['mockup_url'] ) . '" target="_blank">View Mockup</a></p>';
                echo '<p><strong>Instructions:</strong> ' . esc_html( $position['instruction'] ) . '</p>';
            }
        }
    }

    private function call_api( $order ) {
        // Get the API key
        $api_key = get_option( 'be-api-key' ) ?? '';

        // Extract order data
        $order_id   = $order->get_id();
        $order_date = $order->get_date_created()->date( 'Y-m-d' );
        // $delivery_date    = $order->get_date_created()->format( 'Y-m-d' );
        $delivery_date    = ( new \DateTime() )->modify( '+7 days' )->format( 'Y-m-d' );
        $timestamp        = $order->get_date_created()->format( 'Y-m-d\TH:i:s' );
        $contact_email    = $order->get_billing_email();
        $billing_address  = $order->get_address( 'billing' );
        $shipping_address = $billing_address; // Assuming billing and shipping are the same
        // $this->put_program_logs( 'Shipping Addresses: ' . json_encode( $shipping_address ) );
        $contact_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();

        // Prepare order items
        $order_items = [];
        foreach ( $order->get_items() as $item_id => $item ) {
            // Get product details
            $product  = $item->get_product();
            $sku      = $product->get_sku();
            $quantity = $item->get_quantity();
            $total    = $item->get_total();

            $order_items[] = [
                'order_line_id'  => "$item_id",
                'sku'            => $sku,
                'quantity'       => "$quantity",
                'expected_price' => "$total",
            ];
        }

        // Handle product and printing positions
        $product_id         = null;
        $printing_positions = null;
        $custom_print_media = null;
        foreach ( $order->get_items() as $item ) {
            $product_id = $item->get_product()->get_id();
            break; // Just considering the first item for product id in this context
        }

        $cookie_key = "printing_positions_" . $product_id;
        // Put the cookie key to log
        // $this->put_program_logs( 'Cookie Key: ' . $cookie_key );

        // Check for printing positions from cookie
        if ( isset( $_COOKIE[$cookie_key] ) ) {
            // Get cookie values
            $cookie_values = $_COOKIE[$cookie_key];
            // decode $cookie_values
            // Replace \ from cookie values
            $cookie_values = str_replace( '\\', '', $cookie_values );
            // Log the cookie values
            // $this->put_program_logs( 'Cookie Values: ' . $cookie_values );
            // Decode the cookie values
            $cookie_values = json_decode( $cookie_values, true );
            // Set printing positions
            $printing_positions = json_encode( $cookie_values['selectedPrintData'] );
            // Set custom print media
            $custom_print_media = json_encode( $cookie_values['customPrintMedias'] );
        }

        if ( $printing_positions ) {
            // Replace \ from printing positions
            $printing_positions = str_replace( '\\', '', $printing_positions );
            // Log the printing positions
            // $this->put_program_logs( 'Selected Printing Positions: ' . $printing_positions );
            // decode the printing positions
            $printing_positions = json_decode( $printing_positions, true );
        }

        if ( $custom_print_media ) {
            // Put to log
            // $this->put_program_logs( 'Custom Print Media: ' . $custom_print_media );

            // Decode the custom print media
            $custom_print_media = json_decode( $custom_print_media, true );
            $artwork_url        = $custom_print_media['artwork_url'];
            $artwork_url        = str_replace( "\\", "", $artwork_url );
            $mockup_url         = $custom_print_media['mockup_url'];
            $mockup_url         = str_replace( "\\", "", $mockup_url );
            $instructions       = $custom_print_media['instructions'];
        }

        $pantone_color_cookie_key = "_selectedPantoneColors";
        $pantone_colors_cookies   = null;

        if ( isset( $_COOKIE[$pantone_color_cookie_key] ) ) {
            // Get and decode cookie value
            // $pantone_colors_cookies = urldecode( $_COOKIE[$pantone_color_cookie_key] );
            $pantone_colors_cookies = $_COOKIE[$pantone_color_cookie_key];

            // Replace \ from cookie value
            $pantone_colors_cookies = str_replace( '\\', '', $pantone_colors_cookies );

            // Decode JSON after URL decoding
            $pantone_colors_cookies = json_decode( $pantone_colors_cookies, true );

        }

        // Log the final value outside the block
        // $this->put_program_logs( 'Final Pantone Colors: ' . json_encode( $pantone_colors_cookies ) );


        // Determine order type based on printing positions
        $order_type = !empty( $printing_positions ) ? 'PRINT' : 'NORMAL';

        // Build the payload for NORMAL order type
        $normal_order_payload = [
            'order_header' => [
                'preferred_shipping_date' => "",
                'check_price'             => 'false',
                'currency'                => $order->get_currency(),
                'contact_email'           => $contact_email,
                'shipping_address'        => [
                    'contact_name' => trim( $contact_name ),
                    'company_name' => trim( $billing_address['company'] ?? '' ),
                    'street1'      => trim( $billing_address['address_1'] ),
                    'street2'      => trim( $billing_address['address_2'] ?? '' ),
                    'postal_code'  => trim( $billing_address['postcode'] ),
                    'city'         => trim( $billing_address['city'] ),
                    'region'       => trim( $billing_address['state'] ),
                    'country'      => trim( $billing_address['country'] ),
                    'email'        => trim( $contact_email ),
                    'phone'        => trim( $billing_address['phone'] ?? '' ),
                ],
                'po_number'               => "$order_id",
                'timestamp'               => $timestamp,
                'contact_name'            => $contact_name,
                'order_type'              => 'NORMAL',
            ],
            'order_lines'  => $order_items,
        ];

        // Build the payload for PRINT order type
        $print_order_payload = [
            'order_header' => [
                // 'preferred_shipping_date' => '',
                'currency'         => $order->get_currency(),
                'contact_email'    => $contact_email,
                'check_price'      => 'false',
                'shipping_address' => [
                    'contact_name' => trim( $contact_name ),
                    'company_name' => trim( $billing_address['company'] ?? '' ),
                    'street1'      => trim( $billing_address['address_1'] ),
                    'street2'      => trim( $billing_address['address_2'] ?? '' ),
                    'postal_code'  => trim( $billing_address['postcode'] ),
                    'city'         => trim( $billing_address['city'] ),
                    'region'       => trim( $billing_address['state'] ),
                    'country'      => trim( $billing_address['country'] ),
                    'email'        => trim( $contact_email ),
                    'phone'        => trim( $billing_address['phone'] ?? '' ),
                ],
                'po_number'        => "$order_id",
                'timestamp'        => $timestamp,
                'contact_name'     => $contact_name,
                'order_type'       => 'PRINT',
            ],
            'order_lines'  => [],
        ];

        // Add items to the print order payload if it's a PRINT order
        foreach ( $order->get_items() as $item_id => $item ) {

            // Get product data
            $product        = $item->get_product();
            $product_id     = $product->get_id();
            $master_code    = get_post_meta( $product_id, '_master_code', true );
            $color_code     = get_post_meta( $product_id, '_color_code', true );
            $pms_color      = get_post_meta( $product_id, '_pms_color', true );
            $quantity       = $item->get_quantity();
            $expected_price = $item->get_total();

            // Initialize an empty array for storing the dynamic positions
            $_printing_positions = [];

            // Populate printing positions
            if ( !empty( $printing_positions ) && is_array( $printing_positions ) ) {
                foreach ( $printing_positions as $position ) {

                    // Get printing position data
                    $id                     = $position['position_id'];
                    $_transform_position_id = str_replace( ' ', '_', $id );
                    $_print_size_height     = $position['max_print_size_height'];
                    $_print_size_width      = $position['max_print_size_width'];
                    $_printing_technique    = $position['selectedTechniqueId'];
                    $_number_of_color       = $position['maxColors'];

                    // get pantone colors
                    $_pantone_colors = $pantone_colors_cookies[$_transform_position_id]['selectedColors'];

                    $ultimate_pantone_colors = [];
                    if ( !empty( $_pantone_colors ) && is_array( $_pantone_colors ) ) {
                        foreach ( $_pantone_colors as $key => $value ) {
                            // remove # from the beginning
                            $value = ltrim( $value, '#' );

                            // Add to ultimate pantone colors array
                            $ultimate_pantone_colors[] = [
                                "color" => "Pantone $value",
                            ];
                        }
                    }

                    // $this->put_program_logs( 'ultimate_pantone_colors: ' . json_encode( $ultimate_pantone_colors ) );

                    // Populate dynamic printing position data
                    $_printing_positions[] = [
                        'id'                     => "$id",
                        'print_size_height'      => "$_print_size_height",
                        'print_size_width'       => "$_print_size_width",
                        'printing_technique_id'  => "$_printing_technique",
                        'number_of_print_colors' => "$_number_of_color",
                        'print_artwork_url'      => $artwork_url,
                        'print_mockup_url'       => $mockup_url,
                        'print_instruction'      => $instructions,
                        'print_colors'           => $ultimate_pantone_colors,
                    ];
                }
            }

            $print_items = [
                [
                    'item_color_number' => "$color_code",
                    'quantity'          => "$quantity",
                ],
            ];

            // Add item details to the PRINT order lines
            $print_order_payload['order_lines'][] = [
                'order_line_id'      => "$item_id",
                'master_code'        => "$master_code",
                'quantity'           => "$quantity",
                'expected_price'     => "$expected_price",
                'printing_positions' => $_printing_positions,
                'print_items'        => $print_items,
            ];
        }


        // Choose the appropriate payload
        $payload = $order_type === 'PRINT' ? $print_order_payload : $normal_order_payload;

        // Log the payload
        $this->put_program_logs( 'payload: ' . json_encode( $payload, JSON_PRETTY_PRINT ) );

        // Make the API call with the prepared payload
        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL            => 'https://api.midocean.com/gateway/order/2.1/create',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => '',
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_POSTFIELDS     => json_encode( $payload ),
                CURLOPT_HTTPHEADER     => array(
                    'x-Gateway-APIKey: ' . $api_key,
                    'Accept: application/json',
                    'Content-Type: application/json',
                ),
            )
        );

        // Capture and return the response
        $response = curl_exec( $curl );
        curl_close( $curl );

        return $response;
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