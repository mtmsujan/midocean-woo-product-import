(function ($) {
  $(document).ready(function () {
    // Product additional information accordion
    $("#additional-information-accordion").accordion({
      collapsible: true,
      active: false,
    });

    // Handle AJAX request for add to cart
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

      // Get product ID and quantity
      let productId = $(this).data("product-id");
      let quantity = $(".input-quantity").val();

      // AJAX call for add to cart
      $.ajax({
        url: bulkProductImport.ajax_url,
        method: "POST",
        data: {
          action: "custom_add_to_cart",
          product_id: productId,
          quantity: quantity,
        },
        success: function (response) {
          let data = response.data;
          if (data.success) {
            button.removeClass("disabled");
            addToCartLoader.removeClass("loader");

            viewCartButton.attr("href", data.cart_page_url);
            viewCartContainer.removeClass("d-none").fadeIn();
            checkIcon.removeClass("d-none").attr("src", data.check_icon_url);
          } else {
            console.log("Error: " + data.message);
          }
        },
        error: function (xhr, status, error) {
          console.log("AJAX Error: " + error);
          button.removeClass("disabled");
          addToCartLoader.removeClass("loader");
        },
      });
    });

    // Initialize variables
    let selectedPantoneColors = {};
    let currentPositionId = $("#selected-printing-option-ids").val();
    let maxColors = 0;
    const all_color_hex = JSON.parse(bulkProductImport.all_color_hex);
    const input = document.querySelector('input[name="tags"]');
    let tagify;

    // Fetch selected print data from cookie
    function getSelectedPrintData() {
      try {
        const cookieData = document.cookie
          .split("; ")
          .find((row) => row.startsWith("_selectedPrintData="))
          .split("=")[1];
        return JSON.parse(decodeURIComponent(cookieData));
      } catch (error) {
        console.log("Error fetching or parsing _selectedPrintData:", error);
        return [];
      }
    }

    // Update message for selected color limit
    function updateColorSelectionMessage() {
      $(".pantone-color-selection-info").text(
        `Puede seleccionar hasta ${maxColors} colores para esta posición.`
      );
      // console.log(`maxColors updated: ${maxColors}`);
    }

    // Initialize or update Tagify instance
    function initializeTagify() {
      if (tagify) tagify.destroy(); // Destroy existing instance if it exists

      tagify = new Tagify(input, {
        delimiters: null,
        templates: {
          tag: function (tagData) {
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
          },
          dropdownItem: function (tagData) {
            return `<div ${this.getAttributes(
              tagData
            )} class='tagify__dropdown__item ${
              tagData.class ? tagData.class : ""
            }'>
                        <span style="padding:0.2rem 0.45rem!important;background:${
                          tagData.value
                        };border:0.5px solid black;" class="rounded-circle me-2"></span>
                        <span>${tagData.value}</span>
                    </div>`;
          },
        },
        enforceWhitelist: true,
        whitelist: all_color_hex,
        maxTags: maxColors, // Updated dynamically
        focusable: false,
        dropdown: {
          position: "input",
          maxItems: 500,
          highlightFirst: true,
          classname: "tags-look",
          enabled: 0,
        },
      });

      // Attach Tagify event listeners
      tagify.on("add", updateSelectedColors);
      tagify.on("remove", updateSelectedColors);
    }

    // Initial call to setup Tagify
    initializeTagify();

    // Handle changes in the position selection dropdown
    $("#selected-printing-option-ids").change(function () {
      currentPositionId = $(this).val();
      const sanitizeCurrentPositionId = currentPositionId.replace(/\s/g, "_");

      const selectedPrintData = getSelectedPrintData();
      selectedPrintData.forEach((item) => {
        let sanitizedPositionId = item.position_id.replace(/\s/g, "_");

        if (sanitizeCurrentPositionId === sanitizedPositionId) {
          maxColors = item.maxColors;
          updateColorSelectionMessage();
          initializeTagify(); // Reinitialize Tagify with updated maxColors
        }
      });

      updateSelectedColors();
    });

    // Helper function to set a JSON cookie
    function setJSONCookie(name, json, hours) {
      const d = new Date();
      d.setTime(d.getTime() + hours * 60 * 60 * 1000); // Convert hours to milliseconds
      const expires = "expires=" + d.toUTCString();
      const value = encodeURIComponent(JSON.stringify(json)); // Encode JSON for cookie storage
      document.cookie = name + "=" + value + ";" + expires + ";path=/";
    }

    // Update colors when Tagify adds or removes tags
    function updateSelectedColors() {
      if (currentPositionId) {
        const sanitizedPositionId = currentPositionId.replace(/\s/g, "_");
        const selectedColors = tagify.value.map((tag) => tag.value);

        selectedPantoneColors[sanitizedPositionId] = { selectedColors };
        // console.log("Updated selectedPantoneColors:", selectedPantoneColors);

        // Save selectedPantoneColors to cookie with a 1-hour expiration
        setJSONCookie("_selectedPantoneColors", selectedPantoneColors, 1);
      }
    }
  });
})(jQuery);
