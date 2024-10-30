<?php
// Retrieve the base URL of the site
$base_url = get_option( 'home' ) ?? '';
?>

<table>
    <tr>
        <th><?php esc_html_e( 'Endpoint', 'bulk-product-import' ); ?></th>
        <th><?php esc_html_e( 'Name', 'bulk-product-import' ); ?></th>
        <th><?php esc_html_e( 'Action', 'bulk-product-import' ); ?></th>
    </tr>
    <tr>
        <?php
        // Define the server status endpoint
        $server_status = esc_url( $base_url . "/wp-json/bulk-import/v1/server-status" );
        ?>
        <td id="status-api"><?php echo $server_status; ?></td>
        <td><?php esc_html_e( 'Server Status', 'bulk-product-import' ); ?></td>
        <td>
            <button type="button" id="status-cp" class="btn btn-primary btn-sm">
                <?php esc_html_e( 'Copy', 'bulk-product-import' ); ?>
            </button>
        </td>
    </tr>
    <tr>
        <?php
        // Define the delete products endpoint
        $delete_products = esc_url( $base_url . "/wp-json/bulk-import/v1/delete-products" );
        ?>
        <td id="delete-api"><?php echo $delete_products; ?></td>
        <td><?php esc_html_e( 'Delete Products', 'bulk-product-import' ); ?></td>
        <td>
            <button type="button" id="delete-cp" class="btn btn-primary btn-sm">
                <?php esc_html_e( 'Copy', 'bulk-product-import' ); ?>
            </button>
        </td>
    </tr>
    <tr>
        <?php
        // Define the delete trash products endpoint
        $delete_trash_products = esc_url( $base_url . "/wp-json/bulk-import/v1/delete-trash-products" );
        ?>
        <td id="delete-trash-api"><?php echo $delete_trash_products; ?></td>
        <td><?php esc_html_e( 'Delete Trash Products', 'bulk-product-import' ); ?></td>
        <td>
            <button type="button" id="delete-trash-cp" class="btn btn-primary btn-sm">
                <?php esc_html_e( 'Copy', 'bulk-product-import' ); ?>
            </button>
        </td>
    </tr>
    <tr>
        <?php
        // Define the delete Woo categories endpoint
        $delete_woo_cats = esc_url( $base_url . "/wp-json/bulk-import/v1/delete-woo-cats" );
        ?>
        <td id="delete-woo-cats-api"><?php echo $delete_woo_cats; ?></td>
        <td><?php esc_html_e( 'Delete Woo Categories', 'bulk-product-import' ); ?></td>
        <td>
            <button type="button" id="delete-woo-cats-cp" class="btn btn-primary btn-sm">
                <?php esc_html_e( 'Copy', 'bulk-product-import' ); ?>
            </button>
        </td>
    </tr>
    <tr>
        <?php
        // Define the sync products endpoint
        $sync_products = esc_url( $base_url . "/wp-json/bulk-import/v1/sync-products" );
        ?>
        <td id="sync-products-api"><?php echo $sync_products; ?></td>
        <td><?php esc_html_e( 'Sync Products', 'bulk-product-import' ); ?></td>
        <td>
            <button type="button" id="sync-products-cp" class="btn btn-primary btn-sm">
                <?php esc_html_e( 'Copy', 'bulk-product-import' ); ?>
            </button>
        </td>
    </tr>
    <tr>
        <?php
        // Define the sync products endpoint
        $insert_products = esc_url( $base_url . "/wp-json/bulk-import/v1/insert-products-db" );
        ?>
        <td id="insert-products-api"><?php echo $insert_products; ?></td>
        <td><?php esc_html_e( 'Insert Products DB', 'bulk-product-import' ); ?></td>
        <td>
            <button type="button" id="insert-products-cp" class="btn btn-primary btn-sm">
                <?php esc_html_e( 'Copy', 'bulk-product-import' ); ?>
            </button>
        </td>
    </tr>
    <tr>
        <?php
        // Define the sync products endpoint
        $insert_price = esc_url( $base_url . "/wp-json/bulk-import/v1/insert-price-db" );
        ?>
        <td id="insert-price-api"><?php echo $insert_price; ?></td>
        <td><?php esc_html_e( 'Insert Price DB', 'bulk-product-import' ); ?></td>
        <td>
            <button type="button" id="insert-price-cp" class="btn btn-primary btn-sm">
                <?php esc_html_e( 'Copy', 'bulk-product-import' ); ?>
            </button>
        </td>
    </tr>
    <tr>
        <?php
        // Define the sync products endpoint
        $insert_stock = esc_url( $base_url . "/wp-json/bulk-import/v1/insert-stock-db" );
        ?>
        <td id="insert-stock-api"><?php echo $insert_stock; ?></td>
        <td><?php esc_html_e( 'Insert Stock DB', 'bulk-product-import' ); ?></td>
        <td>
            <button type="button" id="insert-stock-cp" class="btn btn-primary btn-sm">
                <?php esc_html_e( 'Copy', 'bulk-product-import' ); ?>
            </button>
        </td>
    </tr>
    <tr>
        <?php
        // Define the sync products endpoint
        $insert_print_data = esc_url( $base_url . "/wp-json/bulk-import/v1/insert-print-data-db" );
        ?>
        <td id="insert-print-api"><?php echo $insert_print_data; ?></td>
        <td><?php esc_html_e( 'Insert Print Data DB', 'bulk-product-import' ); ?></td>
        <td>
            <button type="button" id="insert-print-data-cp" class="btn btn-primary btn-sm">
                <?php esc_html_e( 'Copy', 'bulk-product-import' ); ?>
            </button>
        </td>
    </tr>
    <tr>
        <?php
        // Define the sync products endpoint
        $insert_print__price_data = esc_url( $base_url . "/wp-json/bulk-import/v1/insert-print-price-data-db" );
        ?>
        <td id="insert-print-price-api"><?php echo $insert_print__price_data; ?></td>
        <td><?php esc_html_e( 'Insert Print Price Data DB', 'bulk-product-import' ); ?></td>
        <td>
            <button type="button" id="insert-print-price-data-cp" class="btn btn-primary btn-sm">
                <?php esc_html_e( 'Copy', 'bulk-product-import' ); ?>
            </button>
        </td>
    </tr>
    <tr>
        <?php
        // Define the sync products endpoint
        $insert_print_label_data = esc_url( $base_url . "/wp-json/bulk-import/v1/insert-print-price-data-label-db" );
        ?>
        <td id="insert-print-price-label-api"><?php echo $insert_print_label_data; ?></td>
        <td><?php esc_html_e( 'Insert Print Price Label Data DB', 'bulk-product-import' ); ?></td>
        <td>
            <button type="button" id="insert-print-price-label-data-cp" class="btn btn-primary btn-sm">
                <?php esc_html_e( 'Copy', 'bulk-product-import' ); ?>
            </button>
        </td>
    </tr>
    <tr>
        <?php
        $transform_color_list = esc_url( $base_url . "/wp-json/bulk-import/v1/transform-color-list" );
        ?>
        <td id="transform_color_list_api"><?= $transform_color_list; ?></td>
        <td><?php esc_html_e( 'Transform Color List', 'bulk-product-import' ); ?></td>
        <td>
            <button type="button" id="transform_color_list_cp" class="btn btn-primary btn-sm">
                <?php esc_html_e( 'Copy', 'bulk-product-import' ); ?>
            </button>
        </td>
    </tr>
    <tr>
        <?php
        $insert_color_group_db = esc_url( $base_url . "/wp-json/bulk-import/v1/insert-color-group-db" );
        ?>
        <td id="insert_color_group_api"><?= $insert_color_group_db; ?></td>
        <td><?php esc_html_e( 'Insert Color Group DB', 'bulk-product-import' ); ?></td>
        <td>
            <button type="button" id="insert_color_group_cp" class="btn btn-primary btn-sm">
                <?php esc_html_e( 'Copy', 'bulk-product-import' ); ?>
            </button>
        </td>
    </tr>
    <tr>
        <?php
        $insert_color_hex_db = esc_url( $base_url . "/wp-json/bulk-import/v1/insert-color-hex-db" );
        ?>
        <td id="insert_color_hex_api"><?= $insert_color_hex_db; ?></td>
        <td><?php esc_html_e( 'Insert Color hex DB', 'bulk-product-import' ); ?></td>
        <td>
            <button type="button" id="insert_color_hex_cp" class="btn btn-primary btn-sm">
                <?php esc_html_e( 'Copy', 'bulk-product-import' ); ?>
            </button>
        </td>
    </tr>
</table>