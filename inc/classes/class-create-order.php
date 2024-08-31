<?php

namespace BULK_IMPORT\Inc;

use BULK_IMPORT\Inc\Traits\Singleton;

class Create_Order {

    use Singleton;

    public function __construct() {
        $this->setup_hooks();
    }

    public function setup_hooks() {
        // setup hooks
        add_action( 'woocommerce_thankyou', [ $this, 'create_order' ] );
    }

    public function create_order( $order_id ) {
        // Get order
        $order = wc_get_order( $order_id );

        // Call API
        $api_response = $this->call_api( $order );
        // Put program logs
        $this->put_program_logs( $api_response );
    }

    private function call_api( $order ) {

        // get api key
        $api_key = get_option( 'be-api-key' ) ?? '';

        $order_id         = $order->get_id();
        $order_date       = $order->get_date_created()->date( 'Y-m-d' );
        $contact_email    = $order->get_billing_email();
        $billing_address  = $order->get_address( 'billing' );
        $shipping_address = $billing_address;
        $contact_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
        
        // Check if the order type is NORMAL or PRINT
        // $order_type       = 'NORMAL';
        $order_type       = 'PRINT';

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

        // Create normal order payload array
        $normal_order_payload = [
            'order_header' => [
                'preferred_shipping_date' => $order_date,
                'check_price'             => 'false',
                'currency'                => $order->get_currency(),
                'contact_email'           => $contact_email,
                'shipping_address'        => $shipping_address,
                'po_number'               => $order_id,
                'timestamp'               => date( 'Y-m-d H:i:s' ),
                'contact_name'            => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                'order_type'              => 'NORMAL',
            ],
            'order_lines'  => $order_items,
        ];

        // Create print order payload array
        $print_order_payload = [
            'order_header' => [
                'preferred_shipping_date' => $order_date,
                'currency'                => $order->get_currency(),
                'contact_email'           => $contact_email,
                'check_price'             => 'false',
                'shipping_address'        => [
                    'contact_name'  => $contact_name,
                    'company_name'  => $billing_address['company'] ?? '',
                    'street1'       => $billing_address['address_1'],
                    'postal_code'   => $billing_address['postcode'],
                    'city'          => $billing_address['city'],
                    'region'        => $billing_address['state'],
                    'country'       => $billing_address['country'],
                    'email'         => $contact_email,
                    'phone'         => $billing_address['phone'] ?? ''
                ],
                'po_number'     => $order_id,
                'timestamp'     => date( 'Y-m-d\TH:i:s' ),
                'contact_name'  => $contact_name,
                'order_type'    => 'PRINT',
            ],
            'order_lines' => []
        ];

        foreach ( $order->get_items() as $item_id => $item ) {

            $product   = $item->get_product();
            $product_id = $product->get_id();
            $master_code = get_post_meta( $product_id, '_master_code', true );
            $color_code = get_post_meta( $product_id, '_color_code', true );
            $order_line_id = $item_id;
            $sku = $product->get_sku();
            $quantity = $item->get_quantity();
            $expected_price = $item->get_total();

            // Populate printing positions and print items (this data may come from custom fields)
            $printing_positions = [
                [
                    'id'                    => 'FRONT',
                    'print_size_height'     => '190',
                    'print_size_width'      => '120',
                    'printing_technique_id' => 'S2',
                    'number_of_print_colors'=> '1',
                    'print_artwork_url'     => 'your logo URL', // Replace: with actual URL
                    'print_mockup_url'      => 'your mockup URL', // Replace: with actual URL
                    'print_instruction'     => 'Print instructions',
                    'print_colors'          => [
                        [
                            'color' => 'Pantone 4280C'
                        ]
                    ]
                ]
            ];

            $print_items = [
                [
                    'item_color_number' => $color_code,
                    'quantity'          => $quantity
                ]
            ];

            // Add item to order lines in the print order payload
            $print_order_payload['order_lines'][] = [
                'order_line_id'      => $order_line_id,
                'master_code'        => $master_code,
                'quantity'           => $quantity,
                'expected_price'     => $expected_price,
                'printing_positions' => $printing_positions,
                'print_items'        => $print_items
            ];
        }

        // Choose the appropriate payload based on the order type
        $payload = $order_type === 'PRINT' ? $print_order_payload : $normal_order_payload;

        $this->put_program_logs( 'payload: ' . json_encode( $payload, JSON_PRETTY_PRINT ) );

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

        $response = curl_exec( $curl );

        curl_close( $curl );
        return $response;
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
