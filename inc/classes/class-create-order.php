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

        $order_id                  = $order->get_id();
        $order_date                = $order->get_date_created()->date( 'Y-m-d' );
        $contact_email             = $order->get_billing_email();
        $shipping_address          = $order->get_address( 'shipping' );
        $shipping_address['email'] = $contact_email;

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

        $payload = [
            'order_header' => [
                'preferred_shipping_date' => $order_date,
                'check_price'             => 'false',
                'currency'                => $order->get_currency(),
                'contact_email'           => $contact_email,
                'shipping_address'        => $shipping_address,
                'po_number'               => $order_id,
                'timestamp'               => date( 'Y-m-d H:i:s' ),
                'contact_name'            => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                'order_type'              => 'SAMPLE',
            ],
            'order_lines'  => $order_items,
        ];

        $this->put_program_logs( json_encode( $payload, JSON_PRETTY_PRINT ) );

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

    function put_program_logs( $data ) {

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
