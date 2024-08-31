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

      let button = $(this);
      button.addClass("disabled");
      let addToCartLoader = $(
        ".be-add-to-cart-btn-without-configure .add-to-cart-loader"
      );
      addToCartLoader.addClass("loader");

      let viewCartButton = $(".view-cart-url");
      let viewCartContainer = $(".view-cart-container");
      let checkIcon = $(".check-icon");

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
          // Get response data
          let data = response.data;

          if (data.success) {
            let successMessage = data.message;
            // Reset button and loader after request
            button.removeClass("disabled");
            addToCartLoader.removeClass("loader");

            addToCartLoader.text();
            viewCartButton.attr("href", data.cart_page_url);
            viewCartContainer.removeClass("d-none");
            viewCartContainer.fadeIn();
            checkIcon.removeClass("d-none");
            checkIcon.attr("src", data.check_icon_url);
          } else {
            let errorMessage = data.message;
            // console.log("Error: " + errorMessage);
          }
        },
        error: function (xhr, status, error) {
          console.log("AJAX Error: " + error); // Log any AJAX errors
          button.removeClass("disabled");
          addToCartLoader.removeClass("loader");
        },
      });
    });
  });
})(jQuery);
