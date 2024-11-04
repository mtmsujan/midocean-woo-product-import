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
            console.log("Error: " + errorMessage);
          }
        },
        error: function (xhr, status, error) {
          console.log("AJAX Error: " + error);
          button.removeClass("disabled");
          addToCartLoader.removeClass("loader");
        },
      });
    });
  });
})(jQuery);

const all_color_hex = JSON.parse(bulkProductImport.all_color_hex);

// Tagify configuration
const input = document.querySelector('input[name="tags"]');
const tagify = new Tagify(input, {
  delimiters: null,
  templates: {
    tag: function (tagData) {
      try {
        // _ESCAPE_START_
        return `<tag title='${
          tagData.value
        }' contenteditable='false' spellcheck="false"
                    class='tagify__tag ${
                      tagData.class ? tagData.class : ""
                    }' ${this.getAttributes(tagData)}>
                        <x title='remove tag' class='tagify__tag__removeBtn'></x>
                        <div class="d-flex align-items-center">
                            <span style="width:1rem;height:1rem;background:${
                              tagData.value
                            };border:0.5px solid black;" class="rounded-circle me-2"></span>
                            <span class='tagify__tag-text'>${
                              tagData.value
                            }</span>
                        </div>
                    </tag>`;
        // _ESCAPE_END_
      } catch (err) {}
    },

    dropdownItem: function (tagData) {
      try {
        // _ESCAPE_START_
        return `<div ${this.getAttributes(
          tagData
        )} class='tagify__dropdown__item ${tagData.class ? tagData.class : ""}'>
                            <span style="padding:0.2rem 0.45rem!important;background:${
                              tagData.value
                            };border:0.5px solid black;" class="rounded-circle me-2"></span>
                            <span>${tagData.value}</span>
                        </div>`;
        // _ESCAPE_END_
      } catch (err) {}
    },
  },
  enforceWhitelist: true,
  whitelist: all_color_hex,
  maxTags: 20,
  focusable: false,
  dropdown: {
    position: "input",
    maxItems: 500,
    highlightFirst: true,
    classname: "tags-look",
    enabled: 0,
  },
});
