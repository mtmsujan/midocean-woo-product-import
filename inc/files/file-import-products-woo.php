<?php

/**
 * Import Products to WooCommerce template
 */

defined( "ABSPATH" ) || exit( "Direct Access Not Allowed" );

if ( file_exists( BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/vendor/autoload.php' ) ) {
    require_once BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/vendor/autoload.php';
}

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;

/**
 * Function to insert products into WooCommerce
 * Fetch product data from database
 * Process product data and insert into WooCommerce
 * 
 */
function products_import_woocommerce() {
    try {
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

        // WooCommerce store information
        $website_url     = home_url();
        $consumer_key    = get_option( 'be-client-id' ) ?? '';
        $consumer_secret = get_option( 'be-client-secret' ) ?? '';

        // SQL query to retrieve pending products
        $sql = "SELECT sp.id, sp.product_number, sp.product_data, ss.stock, spr.variant_id, spr.price, spr.valid_until, sp.status  FROM $products_table sp JOIN $stock_table ss ON sp.product_number = ss.product_number JOIN $price_table spr ON sp.product_number = spr.product_number WHERE sp.status = 'pending' LIMIT 1";

        // Retrieve pending products from the database
        $products = $wpdb->get_results( $wpdb->prepare( $sql ) );

        if ( !empty( $products ) && is_array( $products ) ) {
            foreach ( $products as $product ) {

                // Retrieve product data
                $serial_id = $product->id;
                $sku       = $product->product_number;

                // Extract product data
                $product_data = json_decode( $product->product_data, true );

                $title       = $product_data['product_name'];
                $short_desc  = $product_data['short_description'];
                $description = $product_data['long_description'];
                $quantity    = $product->stock;

                // Extract variants
                $variants = $product_data['variants'];

                $category_label1   = '';
                $category_label2   = '';
                $category_label3   = '';
                $color_description = '';
                $color_group       = '';
                $pms_color         = '';
                $gtin              = '';
                $color_code        = null;

                $images = [];
                // Loop through variants for extract images
                if ( !empty( $variants ) && is_array( $variants ) ) {
                    foreach ( $variants as $variant ) {
                        // Extract product category label
                        $category_label1   = $variant['category_level1'];
                        $category_label2   = $variant['category_level2'];
                        $category_label3   = $variant['category_level3'];
                        $color_description = $variant['color_description'];
                        $color_group       = $variant['color_group'];
                        $pms_color         = $variant['pms_color'];
                        $gtin              = $variant['gtin'];
                        $color_code        = $variant['color_code'];

                        // Get digital assets
                        $digital_assets = $variant['digital_assets'];
                        foreach ( $digital_assets as $digital_asset ) {
                            $images[] = $digital_asset['url'];
                        }
                    }
                }

                // Retrieve product category
                $category = $product_data['category_code'];

                // Retrieve product tags
                $tags = '';

                // Extract prices
                // $regular_price = 0;
                $sale_price = $product->price;
                $sale_price = str_replace( ',', '.', $sale_price );

                // Set up the API client with WooCommerce store URL and credentials
                $client = new Client(
                    $website_url,
                    $consumer_key,
                    $consumer_secret,
                    [
                        'verify_ssl' => false,
                        'wp_api'     => true,
                        'version'    => 'wc/v3',
                        'timeout'    => 400,
                    ]
                );

                // Check if the product already exists in WooCommerce
                $args = array(
                    'post_type'  => 'product',
                    'meta_query' => array(
                        array(
                            'key'     => '_sku',
                            'value'   => $sku,
                            'compare' => '=',
                        ),
                    ),
                );

                // Check if the product already exists
                $existing_products = new WP_Query( $args );

                if ( $existing_products->have_posts() ) {
                    $existing_products->the_post();

                    // Get product id
                    $_product_id = get_the_ID();

                    // Update the simple product if it already exists
                    $product_data = [
                        'name'              => $title,
                        'sku'               => $sku,
                        'type'              => 'simple',
                        'description'       => $description,
                        'short_description' => $short_desc,
                        'attributes'        => [],
                    ];

                    // Update product
                    $client->put( 'products/' . $_product_id, $product_data );

                    // Update the status of the processed product in your database
                    $wpdb->update(
                        $products_table,
                        [ 'status' => 'completed' ],
                        [ 'id' => $serial_id ]
                    );

                    // Return success response
                    return new \WP_REST_Response( [
                        'success' => true,
                        'message' => 'Product updated successfully',
                    ] );

                } else {
                    // Create a new simple product if it does not exist
                    $_product_data = [
                        'name'              => $title,
                        'sku'               => $sku,
                        'type'              => 'simple',
                        'description'       => $description,
                        'short_description' => $short_desc,
                        'attributes'        => [],
                    ];

                    // Create the product
                    $_products  = $client->post( 'products', $_product_data );
                    $product_id = $_products->id;

                    // Set product information
                    wp_set_object_terms( $product_id, 'simple', 'product_type' );
                    update_post_meta( $product_id, '_visibility', 'visible' );

                    // Update product stock
                    update_post_meta( $product_id, '_stock', $quantity );

                    // Update product prices
                    // update_post_meta( $product_id, '_regular_price', $regular_price );
                    update_post_meta( $product_id, '_price', $sale_price );

                    // Update product category
                    wp_set_object_terms( $product_id, $category, 'product_cat' );

                    // Update product tags
                    wp_set_object_terms( $product_id, $tags, 'product_tag' );

                    // update product additional information
                    update_product_additional_information( $product_id, $product_data );

                    // update product additional information
                    update_post_meta( $product_id, '_category_level1', $category_label1 );
                    update_post_meta( $product_id, '_category_level2', $category_label2 );
                    update_post_meta( $product_id, '_category_level3', $category_label3 );
                    update_post_meta( $product_id, '_color_description', $color_description );
                    update_post_meta( $product_id, '_color_group', $color_group );
                    update_post_meta( $product_id, '_pms_color', $pms_color );
                    update_post_meta( $product_id, '_ean', $gtin );
                    update_post_meta( $product_id, '_color_code', $color_code );

                    // Display out of stock message if stock is 0
                    if ( $quantity <= 0 ) {
                        update_post_meta( $product_id, '_stock_status', 'outofstock' );
                    } else {
                        update_post_meta( $product_id, '_stock_status', 'instock' );
                    }
                    update_post_meta( $product_id, '_manage_stock', 'yes' );

                    // Set product image gallery and thumbnail
                    if ( $images ) {
                        set_product_images( $product_id, $images );
                    }

                    // Update the status of product in database
                    $wpdb->update(
                        $products_table,
                        [ 'status' => 'completed' ],
                        [ 'id' => $serial_id ]
                    );

                    // Return success response
                    return new \WP_REST_Response( [
                        'success' => true,
                        'message' => 'Product import successfully',
                    ] );
                }
            }
        }
    } catch (HttpClientException $e) {

        echo '<pre><code>' . print_r( $e->getMessage(), true ) . '</code><pre>'; // Error message.
        echo '<pre><code>' . print_r( $e->getRequest(), true ) . '</code><pre>'; // Last request data.
        echo '<pre><code>' . print_r( $e->getResponse(), true ) . '</code><pre>'; // Last response data.

        return new \WP_REST_Response( [
            'success' => false,
            'message' => 'Product import failed.',
        ] );
    }
}

/**
 * Update Product Additional Information
 * @param int $product_id
 * @param array $data
 * @return void
 */
function update_product_additional_information( int $product_id, array $data ) {
    if ( !empty( $product_id ) && is_array( $data ) ) {
        update_post_meta( $product_id, '_master_code', $data['master_code'] );
        update_post_meta( $product_id, '_master_id', $data['master_id'] );
        update_post_meta( $product_id, '_country_of_origin', $data['country_of_origin'] );
        update_post_meta( $product_id, '_type_of_products', $data['type_of_products'] );
        update_post_meta( $product_id, '_commodity_code', $data['commodity_code'] );
        update_post_meta( $product_id, '_number_of_print_positions', $data['number_of_print_positions'] );
        update_post_meta( $product_id, '_brand', $data['brand'] );
        update_post_meta( $product_id, '_product_class', $data['product_class'] );
        update_post_meta( $product_id, '_length', $data['length'] );
        update_post_meta( $product_id, '_length_unit', $data['length_unit'] );
        update_post_meta( $product_id, '_width', $data['width'] );
        update_post_meta( $product_id, '_width_unit', $data['width_unit'] );
        update_post_meta( $product_id, '_height', $data['height'] );
        update_post_meta( $product_id, '_height_unit', $data['height_unit'] );
        update_post_meta( $product_id, '_volume', $data['volume'] );
        update_post_meta( $product_id, '_volume_unit', $data['volume_unit'] );
        update_post_meta( $product_id, '_gross_weight', $data['gross_weight'] );
        update_post_meta( $product_id, '_gross_weight_unit', $data['gross_weight_unit'] );
        update_post_meta( $product_id, '_net_weight', $data['net_weight'] );
        update_post_meta( $product_id, '_net_weight_unit', $data['net_weight_unit'] );
        update_post_meta( $product_id, '_outer_carton_quantity', $data['outer_carton_quantity'] );
        update_post_meta( $product_id, '_carton_length', $data['carton_length'] );
        update_post_meta( $product_id, '_carton_length_unit', $data['carton_length_unit'] );
        update_post_meta( $product_id, '_carton_width', $data['carton_width'] );
        update_post_meta( $product_id, '_carton_width_unit', $data['carton_width_unit'] );
        update_post_meta( $product_id, '_carton_height', $data['carton_height'] );
        update_post_meta( $product_id, '_carton_height_unit', $data['carton_height_unit'] );
        update_post_meta( $product_id, '_carton_volume', $data['carton_volume'] );
        update_post_meta( $product_id, '_carton_gross_weight_unit', $data['carton_gross_weight_unit'] );
        update_post_meta( $product_id, '_material', $data['material'] );

        // Retrieve digital_assets like pdf
        $digital_assets = $data['digital_assets'];
        $digital_assets = json_encode( $digital_assets );
        update_post_meta( $product_id, '_digital_assets', $digital_assets );
    }
}


/**
 * Set Product Images
 *
 * @param int $product_id
 * @param array $images
 * @return void
 */
function set_product_images( $product_id, $images ) {
    if ( !empty( $images ) && is_array( $images ) ) {
        foreach ( $images as $image ) {

            // Extract image name
            $image_name = basename( $image );

            // Get WordPress upload directory
            $upload_dir = wp_upload_dir();

            // Download the image from URL and save it to the upload directory
            $image_data = file_get_contents( $image );

            if ( $image_data !== false ) {
                $image_file = $upload_dir['path'] . '/' . $image_name;
                file_put_contents( $image_file, $image_data );

                // Prepare image data to be attached to the product
                $file_path = $upload_dir['path'] . '/' . $image_name;
                $file_name = basename( $file_path );

                // Insert the image as an attachment
                $attachment = [
                    'post_mime_type' => mime_content_type( $file_path ),
                    'post_title'     => preg_replace( '/\.[^.]+$/', '', $file_name ),
                    'post_content'   => '',
                    'post_status'    => 'inherit',
                ];

                $attach_id = wp_insert_attachment( $attachment, $file_path, $product_id );

                // Add the image to the product gallery
                $gallery_ids   = get_post_meta( $product_id, '_product_image_gallery', true );
                $gallery_ids   = explode( ',', $gallery_ids );
                $gallery_ids[] = $attach_id;
                update_post_meta( $product_id, '_product_image_gallery', implode( ',', $gallery_ids ) );

                // Set the image as the product thumbnail
                set_post_thumbnail( $product_id, $attach_id );

                // if not set post-thumbnail then set a random thumbnail from gallery
                if ( !has_post_thumbnail( $product_id ) ) {
                    if ( !empty( $gallery_ids ) ) {
                        $random_attach_id = $gallery_ids[array_rand( $gallery_ids )];
                        set_post_thumbnail( $product_id, $random_attach_id );
                    }
                }

            }
        }
    }
}

/**
 * Set product images with unique image name
 *
 * @param int $product_id
 * @param array $images
 * @return void
 */
function set_product_images_with_unique_image_name( $product_id, $images ) {
    if ( !empty( $images ) && is_array( $images ) ) {

        $first_image = true;
        $gallery_ids = get_post_meta( $product_id, '_product_image_gallery', true );
        $gallery_ids = !empty( $gallery_ids ) ? explode( ',', $gallery_ids ) : [];

        foreach ( $images as $image_url ) {
            // Extract image name and generate a unique name using product_id
            $image_name        = basename( $image_url );
            $unique_image_name = $product_id . '-' . time() . '-' . $image_name;

            // Get WordPress upload directory
            $upload_dir = wp_upload_dir();

            // Download the image from URL and save it to the upload directory
            $image_data = file_get_contents( $image_url );

            if ( $image_data !== false ) {
                $image_file = $upload_dir['path'] . '/' . $unique_image_name;
                file_put_contents( $image_file, $image_data );

                // Prepare image data to be attached to the product
                $file_path = $upload_dir['path'] . '/' . $unique_image_name;
                $file_name = basename( $file_path );

                // Insert the image as an attachment
                $attachment = [
                    'post_mime_type' => mime_content_type( $file_path ),
                    'post_title'     => preg_replace( '/\.[^.]+$/', '', $file_name ),
                    'post_content'   => '',
                    'post_status'    => 'inherit',
                ];

                $attach_id = wp_insert_attachment( $attachment, $file_path, $product_id );

                // You need to generate the attachment metadata and update the attachment
                require_once( ABSPATH . 'wp-admin/includes/image.php' );
                $attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );
                wp_update_attachment_metadata( $attach_id, $attach_data );

                // Add the image to the product gallery
                $gallery_ids[] = $attach_id;

                // Set the first image as the featured image
                if ( $first_image ) {
                    set_post_thumbnail( $product_id, $attach_id );
                    $first_image = false;
                }
            }
        }

        // Update the product gallery meta field
        update_post_meta( $product_id, '_product_image_gallery', implode( ',', $gallery_ids ) );
    }
}