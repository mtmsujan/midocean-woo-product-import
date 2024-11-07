<?php if ( 'textile' === $this->type_of_product ) : ?>

<!-- Color and multiple size, stock field -->
<div class="product-color-size-stock-fields">
    <!-- Color input row configurator. repeater -->
    <div class="color-input-container">
        <div class="row mt-3 justify-content-start align-items-center color-input-row">
            <!-- Color dropdown -->
            <div class="col-sm-2">
                <div class=" color-dropdown-wrapper">
                    <!-- First displayed button -->
                    <button class="" type="button" id="colorDropdown" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <div class="row align-items-center">
                            <div class="col-4 color-preview">
                                <div style="height: 35px; width: 35px; background: <?= $this->color_hex ?>; border: 1px solid black;"
                                    class="rounded-circle"></div>
                            </div>
                            <div class="col-8 color-name"><?= ucfirst( $this->color_description ); ?> -
                            </div>
                        </div>
                    </button>
                </div>
            </div>
            <!-- /Color dropdown -->
            <!-- Stock input field -->
            <div class="col-sm-10">
                <div class="row">
                    <!-- Repeat: Single size -->
                    <div class="col-3" data-product-sku="XSS">
                        <div class="d-flex flex-column align-content-center">
                            <div class="text-center size-name">XSS</div>
                            <div class="size-quantity-field">
                                <input @keyup="priceCalculationWithoutPrintingCost($el.value, <?php echo $this->product_price; ?>)" class="text-center" type="number" name="" id="" x-model="quantity">
                            </div>
                            <div class="text-center size-stock-value">
                                <span class="stock-value">
                                    120
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- /Repeat: Single size -->
                </div>
            </div>
            <!-- /Stock input field -->
        </div>
    </div>
</div>
<!-- /Color and multiple size, stock field -->

<?php else : ?>

<!-- Color and Quantity field -->
<div class="product-color-and-quantity-field">
    <!-- Color input row configurator. repeater -->
    <div class="color-input-container">
        <div class="row mt-3 justify-content-between align-items-center color-input-row">
            <!-- Color dropdown -->
            <div class="col-sm-2">
                <div class=" color-dropdown-wrapper">
                    <!-- First displayed button -->
                    <button class="" type="button" id="colorDropdown" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <div class="row align-items-center">
                            <div class="col-4 color-preview">
                                <div style="height: 35px; width: 35px; background: <?= $this->color_hex ?>; border: 1px solid black;"
                                    class="rounded-circle"></div>
                            </div>
                            <div class="col-8 color-name"><?= ucfirst( $this->color_description ); ?> -
                            </div>
                        </div>
                    </button>
                </div>
            </div>
            <!-- /Color dropdown -->
            <!-- Stock input field -->
            <div class="col-sm-6">
                <div class="row flex-column">
                    <div class="col-sm-12">
                        <div class="input-quantity-wrapper">
                            <input type="number"
                                @keyup="priceCalculationWithoutPrintingCost($el.value, <?php echo $this->product_price; ?>)"
                                placeholder="0" class="input-quantity" x-model="quantity" />
                        </div>
                    </div>
                    <div class="col-sm-12 mt-1">
                        <div class="row justify-content-around align-items-center">
                            <div class="col-4">
                                <div class="stock-data">
                                    <div><span class="stock-value">
                                            <?php echo $this->product_stock; ?></span> En Stock</div>
                                </div>
                            </div>
                            <div class="col-8"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Stock input field -->
            <!-- Remove color row button -->
            <div class="col-sm-4 remove-color-row">
                <div class="row justify-content-between align-items-center">
                    <div class="col-sm-6"></div>
                    <div class="col-sm-6 text-end close-button">
                        <!-- <i class="fa-solid fa-xmark"></i> -->
                    </div>
                </div>
            </div>
            <!-- /Remove color row button -->
        </div>
    </div>
</div>
<!-- /Color and Quantity field -->

<?php endif; ?>