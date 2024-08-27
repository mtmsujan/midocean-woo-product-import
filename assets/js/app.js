(function ($) {
  $(document).ready(function () {
    // product additional information accordion
    $("#additional-information-accordion").accordion({
      collapsible: true,
      active: false,
    });

    // color input row repeater

    /* $("#add-more-color-button").on("click", function () {
      var colorInputRow = $(".color-input-row:first").clone();
      colorInputRow.find("input").val("");
      colorInputRow.find(".stock-value").text("100");
      colorInputRow.find(".color-name").text("Red -");
      colorInputRow.find(".color-preview circle").attr("fill", "");
      colorInputRow.appendTo(".color-input-container");

      // Conditionally show the close button if there is more than one row
      if ($(".color-input-row").length > 1) {
        $(".color-input-row").find(".close-button").show();
      } else {
        $(".color-input-row").find(".close-button").hide();
      }
    });

    // Event delegation to handle color selection in the dropdown
    $(document).on("click", ".dropdown-item", function (e) {
      e.preventDefault();
      var selectedColor = $(this).data("color");
      var selectedColorName = $(this).find(".color-name").text();

      // Update the button with the selected color
      var button = $(this).closest(".dropdown").find(".dropdown-toggle");
      button.find(".color-preview circle").attr("fill", selectedColor);
      button.find(".color-name").text(selectedColorName);
    }); */

    // Event delegation to remove color row when 'x' is clicked
    /* $(document).on("click", ".close-button", function () {
      if ($(".color-input-row").length > 1) {
        $(this).closest(".color-input-row").remove(); // Remove the row if there is more than one row
      }

      // Conditionally hide the close button if only one row remains
      if ($(".color-input-row").length === 1) {
        $(".color-input-row .close-button").hide();
      }
    });

    // Initially hide the close button if there is only one row
    if ($(".color-input-row").length === 1) {
      $(".color-input-row .close-button").hide();
    } */

    // Add event listeners to all "Clear Selection" buttons
    $(".modal-item-clear-button").on("click", function () {
      // Find and uncheck all radio buttons within the corresponding modal section
      $(this)
        .closest(".col-sm-3")
        .find('input[type="radio"]')
        .prop("checked", false);
    });
  });
})(jQuery);
