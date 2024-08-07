<?php
// Get WooCommerce API credentials from the WordPress options
$client_id     = get_option( 'be-client-id' ) ?? '';
$client_secret = get_option( 'be-client-secret' ) ?? '';
$api_key       = get_option( 'be-api-key' ) ?? '';
?>

<!-- API credentials form -->
<div class="container-fluid api-credentials">
    <div class="row">
        <div class="col-sm-12">
            <!-- Title for the API credentials section -->
            <h4 class="text-center mb-3">
                <?php esc_html_e( 'WooCommerce API Credentials', 'bulk-product-import' ); ?>
            </h4>
            <!-- Form for entering WooCommerce API credentials -->
            <form id="client-credentials-form">
                <div class="d-flex align-items-center mt-2">
                    <!-- Label and input for Client ID -->
                    <label class="form-label" for="client-id">
                        <?php esc_html_e( 'Client ID', 'bulk-product-import' ); ?>
                    </label>
                    <input type="password" class="form-control" style="width: 60% !important; margin-left: 4.7rem;"
                        name="client-id" id="client-id" value="<?php echo esc_attr( $client_id ); ?>"
                        placeholder="<?php esc_attr_e( 'Client ID', 'bulk-product-import' ); ?>" required>
                </div>
                <div class="d-flex align-items-center mt-3">
                    <!-- Label and input for Client Secret -->
                    <label class="form-label" for="client-secret">
                        <?php esc_html_e( 'Client Secret', 'bulk-product-import' ); ?>
                    </label>
                    <input type="password" class="form-control ms-5" style="width: 60% !important" name="client-secret"
                        id="client-secret" value="<?php echo esc_attr( $client_secret ); ?>"
                        placeholder="<?php esc_attr_e( 'Client Secret', 'bulk-product-import' ); ?>" required>
                </div>
                <div class="d-flex align-items-center mt-3">
                    <!-- Label and input for Client Secret -->
                    <label class="form-label" for="be-api-key">
                        <?php esc_html_e( 'API Key', 'bulk-product-import' ); ?>
                    </label>
                    <input type="password" class="form-control ms-5" style="width: 60% !important" name="be-api-key"
                        id="be-api-key" value="<?php echo esc_attr( $api_key ); ?>"
                        placeholder="<?php esc_attr_e( 'API Key', 'bulk-product-import' ); ?>">
                </div>
                <!-- Submit button to save credentials -->
                <input type="submit" class="btn btn-primary mt-3" id="credential-save"
                    value="<?php esc_attr_e( 'Save', 'bulk-product-import' ); ?>">
            </form>
        </div>
    </div>
</div>