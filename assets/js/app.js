(function ($) {
  $(document).ready(function () {
    // product additional information accordion
    $("#additional-information-accordion").accordion({
      collapsible: true,
      active: false,
    });

    // Handle ajax request for add to cart
    $(".be-add-to-cart-btn-without-configure").click(function (e) {
      e.preventDefault();

      // Get product id
      let productId = $(this).data("product-id");
      let quantity = $(".input-quantity").val();

      // ajax call
      $.ajax({
        url: bulkProductImport.ajax_url,
        method: "POST",
        data: {
          action: "custom_add_to_cart",
          product_id: productId,
          quantity: quantity,
        },
        success: function (response) {
          console.log(response);
        },
      });
    });
  });
})(jQuery);
