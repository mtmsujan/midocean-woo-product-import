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

    // Parse all color hex string to JSON
    const all_color_hex = JSON.parse(bulkProductImport.all_color_hex);

    // Filter the array to keep only four or seven character hex codes
    let filtered_color_hex = all_color_hex.filter((hex) => {
      // Check for 4 or 7 character lengths (including the '#')
      return /^#[a-f0-9]{3}$|^#[a-f0-9]{6}$/.test(hex.toLowerCase());
    });

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
        whitelist: filtered_color_hex,
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

    // Initialize Tagify
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
          initializeTagify();
        }
      });

      updateSelectedColors();
    });

    // Helper function to set a JSON cookie
    function setJSONCookie(name, json, hours) {
      const d = new Date();
      d.setTime(d.getTime() + hours * 60 * 60 * 1000);
      const expires = "expires=" + d.toUTCString();
      const value = JSON.stringify(json);
      document.cookie = name + "=" + value + ";" + expires + ";path=/";
    }

    // Update colors when Tagify adds or removes tags
    function updateSelectedColors() {
      console.log(currentPositionId);
      if (currentPositionId) {
        const sanitizedPositionId = currentPositionId.replace(/\s/g, "_");
        const selectedColors = tagify.value.map((tag) => tag.value);

        selectedPantoneColors[sanitizedPositionId] = { selectedColors };
        // console.log("Updated selectedPantoneColors:", selectedPantoneColors);

        // Save selectedPantoneColors to cookie with a 1-hour expiration
        setJSONCookie("_selectedPantoneColors", selectedPantoneColors, 1);
      }else{
        console.log("currentPositionId not found");
      }
    }

    function getCookie(name) {
      const nameEQ = name + "=";
      const cookies = document.cookie.split(";");

      for (let i = 0; i < cookies.length; i++) {
        let cookie = cookies[i].trim();

        if (cookie.indexOf(nameEQ) === 0) {
          return cookie.substring(nameEQ.length, cookie.length);
        }
      }

      return null; // Return null if the cookie is not found
    }

    $(".personalize-button button").click(function () {
      currentPositionId = $("#selected-printing-option-ids").val()
      // get maxColor cookie value key is _max_colors
      const maxColorsCookie = getCookie("_max_colors");
      maxColors = parseInt(maxColorsCookie, 10);
      updateColorSelectionMessage();
      initializeTagify();
      updateSelectedColors();
    });
  });
})(jQuery);
