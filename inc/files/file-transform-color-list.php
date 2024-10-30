<?php

function transform_color_list() {

    // color list file path
    $file   = BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/uploads/color-list.json';
    $colors = file_get_contents( $file );
    $colors = json_decode( $colors, true );

    // extract color group and color name
    $color_group       = $colors['colourGroups'];
    $color_name_values = $colors['colours'];

    // initialize color group result
    $color_group_result = [];
    // generate color group
    foreach ( $color_group as $value ) {
        $color_id = $value['id'];
        $hex      = $value['hex'];
        $name_es  = $value['name']['es_ES'];

        $color_group_result[$color_id] = [
            'id'   => $color_id,
            'name' => $name_es,
            'hex'  => $hex,
        ];
    }

    // encode to json and save to file
    $color_group_result         = json_encode( $color_group_result, JSON_UNESCAPED_SLASHES );
    $color_group_json_file_path = BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/inc/files/color-group.json';
    file_put_contents( $color_group_json_file_path, $color_group_result );

    // initialize color name result
    $color_name_values_result = [];
    // generate color name
    foreach ( $color_name_values as $value ) {

        $color_id = $value['id'];
        $hex      = $value['hex'];
        $name_es  = $value['name']['es_ES'];
        $group    = $value['group'];

        $color_name_values_result[$color_id] = [
            'id'    => $color_id,
            'name'  => $name_es,
            'hex'   => $hex,
            'group' => $group,
        ];
    }

    // encode to json and save to file
    $color_name_values_result         = json_encode( $color_name_values_result, JSON_UNESCAPED_SLASHES );
    $color_name_values_json_file_path = BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/inc/files/color-name.json';
    file_put_contents( $color_name_values_json_file_path, $color_name_values_result );

    return "Color list transformed successfully.";
}