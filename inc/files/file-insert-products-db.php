<?php

// TRUNCATE Table
function truncate_table( $table_name ) {
    global $wpdb;
    $wpdb->query( "TRUNCATE TABLE $table_name" );
}

// fetch products from api
function fetch_products_from_api() {

    // get api key
    $api_key = get_option( 'be-api-key' ) ?? '';

    $curl = curl_init();
    curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL            => 'https://api.midocean.com/gateway/products/2.0?language=en',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => array(
                'x-Gateway-APIKey: ' . $api_key,
            ),
        )
    );

    $response = curl_exec( $curl );

    curl_close( $curl );
    return $response;
}

// insert products to database
function insert_products_db() {

    // Fetch api response
    $api_response = fetch_products_from_api();

    // file path
    // $file = BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/uploads/products.json';
    // file_put_contents( $file, $api_response ); 

    // Decode to array
    $products = json_decode( $api_response, true );

    // Insert to database
    global $wpdb;
    $table_prefix   = get_option( 'be-table-prefix' ) ?? '';
    $products_table = $wpdb->prefix . $table_prefix . 'sync_products';
    truncate_table( $products_table );

    foreach ( $products as $product ) {

        // Extract product variants
        $product_variants = $product['variants'];
        $product_number   = '';

        // Loop through variants to get product number/sku
        foreach ( $product_variants as $variant ) {
            $product_number = $variant['sku'];
        }

        // extract products
        $product_data = json_encode( $product );

        $wpdb->insert(
            $products_table,
            [
                'product_number' => $product_number,
                'product_data'   => $product_data,
                'status'         => 'pending',
            ]
        );
    }

    return '<h4>Products inserted successfully DB</h4>';
}

// fetch price from api
function fetch_price_from_api() {

    // get api key
    $api_key = get_option( 'be-api-key' ) ?? '';

    $curl = curl_init();
    curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL            => 'https://api.midocean.com/gateway/pricelist/2.0/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => array(
                'x-Gateway-APIKey: ' . $api_key,
            ),
        )
    );

    $response = curl_exec( $curl );

    curl_close( $curl );
    return $response;

}

// insert price to database
function insert_price_db() {

    $api_response        = fetch_price_from_api();
    $decode_api_response = json_decode( $api_response, true );
    $prices              = $decode_api_response['price'];

    // Insert to database
    global $wpdb;
    $table_prefix = get_option( 'be-table-prefix' ) ?? '';
    $price_table  = $wpdb->prefix . $table_prefix . 'sync_price';
    truncate_table( $price_table );

    foreach ( $prices as $price ) {

        $wpdb->insert(
            $price_table,
            [
                'product_number' => $price['sku'],
                'variant_id'     => $price['variant_id'],
                'price'          => $price['price'],
                'valid_until'    => $price['valid_until'],
            ]
        );
    }

    return '<h4>Prices inserted successfully DB</h4>';

}

// fetch price from api
function fetch_stock_from_api() {

    // get api key
    $api_key = get_option( 'be-api-key' ) ?? '';

    $curl = curl_init();
    curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL            => 'https://api.midocean.com/gateway/stock/2.0',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => array(
                'x-Gateway-APIKey: ' . $api_key,
            ),
        )
    );

    $response = curl_exec( $curl );

    curl_close( $curl );
    return $response;

}

// insert stock to database
function insert_stock_db() {

    $api_response        = fetch_stock_from_api();
    $decode_api_response = json_decode( $api_response, true );
    $stocks              = $decode_api_response['stock'];

    // Insert to database
    global $wpdb;
    $table_prefix = get_option( 'be-table-prefix' ) ?? '';
    $stock_table  = $wpdb->prefix . $table_prefix . 'sync_stock';
    truncate_table( $stock_table );

    foreach ( $stocks as $stock ) {

        $wpdb->insert(
            $stock_table,
            [
                'product_number' => $stock['sku'],
                'stock'          => $stock['qty'],
            ]
        );
    }

    return '<h4>Stocks inserted successfully DB</h4>';

}

// Fetch product print data from api
function fetch_product_print_data() {

    // get api key
    $api_key = get_option( 'be-api-key' ) ?? '';

    $curl = curl_init();
    curl_setopt_array( $curl, array(
        CURLOPT_URL            => 'https://api.midocean.com/gateway/printdata/1.0',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'GET',
        CURLOPT_HTTPHEADER     => array(
            'x-Gateway-APIKey: ' . $api_key,
        ),
    ) );

    $response = curl_exec( $curl );

    curl_close( $curl );
    return $response;
}

// Insert product print data to database
function insert_product_print_data_db() {

    // Get api response
    $api_response        = fetch_product_print_data();
    $decode_api_response = json_decode( $api_response, true );
    $products            = $decode_api_response['products'];

    // Insert to database
    global $wpdb;
    $table_prefix              = get_option( 'be-table-prefix' ) ?? '';
    $products_print_data_table = $wpdb->prefix . $table_prefix . 'sync_products_print_data';
    truncate_table( $products_print_data_table );

    if ( !empty( $products ) && is_array( $products ) ) {
        foreach ( $products as $product ) {

            // Extract data
            $master_code = $product['master_code'];
            $data        = json_encode( $product );

            // Insert data
            $wpdb->insert(
                $products_print_data_table,
                [
                    'master_code' => $master_code,
                    'print_data'  => $data,
                ]
            );
        }
    }

    return '<h4>Product print data inserted successfully DB</h4>';
}

// Insert product print data to database
function insert_product_print_data_labels_db() {

    // Get api response
    $api_response        = fetch_product_print_data();
    $decode_api_response = json_decode( $api_response, true );
    $labels              = $decode_api_response['printing_technique_descriptions'];

    // Insert to database
    global $wpdb;
    $table_prefix            = get_option( 'be-table-prefix' ) ?? '';
    $labels_print_data_table = $wpdb->prefix . $table_prefix . 'sync_products_print_data_labels';
    truncate_table( $labels_print_data_table );

    // initialize labels_array
    $labels_array = [];

    if ( !empty( $labels ) && is_array( $labels ) ) {
        foreach ( $labels as $label ) {

            // Extract data
            $id      = $label['id'];
            $name_cs = $label['name'][0]['cs'];

            // Build the new structure where the key is the id and the value contains a 'label' field
            $labels_array[$id] = [
                'label' => $name_cs,
            ];

            // encode labels data
            // $labels = json_encode( $label );

            // Insert data
            /* $wpdb->insert(
                $labels_print_data_table,
                [
                    'label_id' => $id,
                    'label_cs' => $name_cs,
                    'labels'   => $labels,
                ]
            ); */
        }
    }

    // store to file
    $file = BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/inc/files/labels.json';
    file_put_contents( $file, json_encode( $labels_array ) );

    // return '<h4>Product print data labels inserted successfully DB</h4>';
    return '<h4>Transformed labels saved to file</h4>';
}

// Fetch product print price data from api
function fetch_product_print_price_data() {

    $api_key = get_option( 'be-api-key' ) ?? '';

    $curl = curl_init();

    curl_setopt_array( $curl, array(
        CURLOPT_URL            => 'https://api.midocean.com/gateway/printpricelist/2.0/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'GET',
        CURLOPT_HTTPHEADER     => array(
            'x-Gateway-APIKey: ' . $api_key,
        ),
    ) );

    $response = curl_exec( $curl );

    curl_close( $curl );
    return $response;
}

// Insert product print price data to database
function insert_product_print_price_data_db() {

    // Get api response
    $api_response        = fetch_product_print_price_data();
    $decode_api_response = json_decode( $api_response, true );
    $prices              = $decode_api_response['print_techniques'];

    // Insert to database
    global $wpdb;
    $table_prefix                    = get_option( 'be-table-prefix' ) ?? '';
    $products_print_price_data_table = $wpdb->prefix . $table_prefix . 'sync_print_price';
    truncate_table( $products_print_price_data_table );

    if ( !empty( $prices ) && is_array( $prices ) ) {
        foreach ( $prices as $price ) {

            // Extract data
            $price_id = $price['id'];
            $data     = json_encode( $price );

            // Insert data
            $wpdb->insert(
                $products_print_price_data_table,
                [
                    'price_id'   => $price_id,
                    'price_data' => $data,
                ]
            );
        }
    }

    return '<h4>Product print price data inserted successfully DB</h4>';
}