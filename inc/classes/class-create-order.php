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
    }

    public function create_order( $order_id ) {
        // Get order details
        $order = wc_get_order( $order_id );

        // Call the API with the order details
        $api_response = $this->call_api( $order );

        // Log the API response
        $this->put_program_logs( $api_response );

        // Handle product and printing positions
        $product_id = null;
        foreach ( $order->get_items() as $item ) {
            $product_id = $item->get_product()->get_id();
            break; // Just considering the first item for product id in this context
        }

        // Cookie key for printing positions
        $cookie_key = "printing_positions_" . $product_id;

        // Check and delete the printing positions cookie after the order is complete
        if ( isset( $_COOKIE[$cookie_key] ) ) {
            // Delete the cookie by setting its expiration to the past
            setcookie( $cookie_key, '', time() - 3600, '/' );
        }
    }

    private function call_api( $order ) {
        // Get the API key
        $api_key = get_option( 'be-api-key' ) ?? '';

        // Extract order data
        $order_id         = $order->get_id();
        $order_date       = $order->get_date_created()->date( 'Y-m-d' );
        $contact_email    = $order->get_billing_email();
        $billing_address  = $order->get_address( 'billing' );
        $shipping_address = $billing_address; // Assuming billing and shipping are the same
        $contact_name     = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();

        // Prepare order items
        $order_items = [];
        foreach ( $order->get_items() as $item_id => $item ) {
            $product       = $item->get_product();
            $order_items[] = [
                'order_line_id'  => $item_id,
                'sku'            => $product->get_sku(),
                'quantity'       => $item->get_quantity(),
                'expected_price' => $item->get_total(),
            ];
        }

        // Handle product and printing positions
        $product_id         = null;
        $printing_positions = null;
        foreach ( $order->get_items() as $item ) {
            $product_id = $item->get_product()->get_id();
            break; // Just considering the first item for product id in this context
        }

        $cookie_key = "printing_positions_" . $product_id;

        // Check for printing positions from cookie
        if ( isset( $_COOKIE[$cookie_key] ) ) {
            $printing_positions = $_COOKIE[$cookie_key];
        }

        if ( $printing_positions ) {
            // Replace \ from printing positions
            $printing_positions = str_replace( '\\', '', $printing_positions );
            // Log the printing positions
            // $this->put_program_logs( $printing_positions );
            // decode the printing positions
            $printing_positions = json_decode( $printing_positions, true );
        }

        // Determine order type based on printing positions
        $order_type = !empty( $printing_positions ) ? 'PRINT' : 'NORMAL';

        // Build the payload for NORMAL order type
        $normal_order_payload = [
            'order_header' => [
                'preferred_shipping_date' => $order_date,
                'check_price'             => 'false',
                'currency'                => $order->get_currency(),
                'contact_email'           => $contact_email,
                'shipping_address'        => $shipping_address,
                'po_number'               => $order_id,
                'timestamp'               => date( 'Y-m-d H:i:s' ),
                'contact_name'            => $contact_name,
                'order_type'              => 'NORMAL',
            ],
            'order_lines'  => $order_items,
        ];

        // Build the payload for PRINT order type
        $print_order_payload = [
            'order_header' => [
                'preferred_shipping_date' => $order_date,
                'currency'                => $order->get_currency(),
                'contact_email'           => $contact_email,
                'check_price'             => 'false',
                'shipping_address'        => [
                    'contact_name' => $contact_name,
                    'company_name' => $billing_address['company'] ?? '',
                    'street1'      => $billing_address['address_1'],
                    'postal_code'  => $billing_address['postcode'],
                    'city'         => $billing_address['city'],
                    'region'       => $billing_address['state'],
                    'country'      => $billing_address['country'],
                    'email'        => $contact_email,
                    'phone'        => $billing_address['phone'] ?? ''
                ],
                'po_number'               => $order_id,
                'timestamp'               => date( 'Y-m-d\TH:i:s' ),
                'contact_name'            => $contact_name,
                'order_type'              => 'PRINT',
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
            $quantity       = $item->get_quantity();
            $expected_price = $item->get_total();

            // Initialize an empty array for storing the dynamic positions
            $_printing_positions = [];

            // Populate printing positions
            if ( !empty( $printing_positions ) && is_array( $printing_positions ) ) {
                foreach ( $printing_positions as $position ) {
                    // Populate dynamic printing position data
                    $_printing_positions[] = [
                        'id'                     => $position['position_id'],
                        'print_size_height'      => $position['max_print_size_height'],
                        'print_size_width'       => $position['max_print_size_width'],
                        'printing_technique_id'  => $position['selectedTechniqueId'],
                        'number_of_print_colors' => $position['maxColors'],
                        'print_artwork_url'      => 'your logo URL',
                        'print_mockup_url'       => 'your mockup URL', 
                        'print_instruction'      => 'Print instructions', 
                        'print_colors'           => [
                            [
                                'color' => 'Pantone 4280C',
                            ],
                        ],
                    ];
                }
            }

            $print_items = [
                [
                    'item_color_number' => $color_code,
                    'quantity'          => $quantity,
                ],
            ];

            // Add item details to the PRINT order lines
            $print_order_payload['order_lines'][] = [
                'order_line_id'      => $item_id,
                'master_code'        => $master_code,
                'quantity'           => $quantity,
                'expected_price'     => $expected_price,
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
                CURLOPT_URL            => 'https://api.midocean.com.bd/gateway/order/2.1/create',
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
