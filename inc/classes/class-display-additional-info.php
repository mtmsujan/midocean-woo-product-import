<?php

namespace BULK_IMPORT\Inc;

use BULK_IMPORT\Inc\Traits\Singleton;

class Display_Additional_Info {

    use Singleton;

    public function __construct() {
        $this->setup_hooks();
    }

    public function setup_hooks() {
        // Setup hooks
        add_filter( 'woocommerce_display_product_attributes', [ $this, 'display_additional_info' ], 10, 2 );
    }

    public function display_additional_info( $product_attributes, $product ) {
        // Retrieve product id
        $product_id = $product->get_id();

        // Retrieve product additional info
        $metadata_keys = [
            '_master_code',
            '_master_id',
            '_type_of_products',
            '_commodity_code',
            '_number_of_print_positions',
            '_brand',
            '_product_class',
            '_length',
            '_length_unit',
            '_width',
            '_width_unit',
            '_height',
            '_height_unit',
            '_volume',
            '_volume_unit',
            '_gross_weight',
            '_gross_weight_unit',
            '_net_weight',
            '_net_weight_unit',
            '_outer_carton_quantity',
            '_carton_length',
            '_carton_length_unit',
            '_carton_width',
            '_carton_width_unit',
            '_carton_height',
            '_carton_height_unit',
            '_carton_volume',
            '_carton_gross_weight_unit',
            '_material',
        ];

        // Loop through each metadata key and retrieve its value
        foreach ( $metadata_keys as $meta_key ) {
            // Get metadata value
            $meta_value = get_post_meta( $product_id, $meta_key, true );

            // If metadata value exists, add it to the product attributes
            if ( !empty( $meta_value ) ) {
                $label                                         = ucwords( str_replace( '_', ' ', substr( $meta_key, 1 ) ) );
                $product_attributes[sanitize_title( $meta_key )] = [
                    'label' => $label,
                    'value' => $meta_value,
                ];
            }
        }

        // Retrieve digital assets
        $digital_assets = get_post_meta( $product_id, '_digital_assets', true );
        $digital_assets = json_decode( $digital_assets, true );

        // Format the digital assets
        if ( !empty( $digital_assets ) ) {
            $formatted_assets = '';
            foreach ( $digital_assets as $asset ) {
                if ( isset( $asset['url'] ) && isset( $asset['subtype'] ) ) {
                    $formatted_assets .= '<a href="' . esc_url( $asset['url'] ) . '" target="_blank">' . esc_html( $asset['url'] ) . '</a> (' . esc_html( $asset['subtype'] ) . ')<br>';
                }
            }

            // Add the formatted digital assets to the product attributes
            if ( !empty( $formatted_assets ) ) {
                $product_attributes['digital_assets'] = [
                    'label' => __( 'Digital Assets', 'your-textdomain' ),
                    'value' => $formatted_assets,
                ];
            }
        }

        return $product_attributes;
    }
}
