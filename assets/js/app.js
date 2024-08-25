(function ($) {
  $(document).ready(function () {
    // product additional information accordion
    $("#additional-information-accordion").accordion({
      collapsible: true,
      active: false,
    });

    // color input row repeater
    // Function to add more color rows
    $("#add-more-color-button").on("click", function () {
      var colorInputRow = $(".color-input-row:first").clone(); // Clone the first color input row
      colorInputRow.find("input").val(""); // Reset input fields
      colorInputRow.find(".stock-value").text("100"); // Reset stock value if needed
      colorInputRow.appendTo(".color-input-container"); // Append the cloned row inside the container
    });

    // Function to remove color row when 'x' is clicked
    $(document).on("click", ".close-button", function () {
      if ($(".color-input-row").length > 1) {
        $(this).closest(".color-input-row").remove(); // Remove the row only if there is more than one row
      }
    });

    $(".dropdown-item").on("click", function (e) {
      e.preventDefault();
      var selectedColor = $(this).data("color");
      var selectedColorName = $(this).find(".color-name").text();

      // Update the button with the selected color
      var button = $(this).closest(".dropdown").find(".dropdown-toggle");
      button.find(".color-preview circle").attr("fill", selectedColor);
      button.find(".color-name").text(selectedColorName);

      // Make API call based on selected color (this can be done via AJAX)
      // Example: Call your API using jQuery.ajax or fetch
      // $.ajax({
      //     url: 'your-api-url',
      //     method: 'POST',
      //     data: { color: selectedColor },
      //     success: function(response) {
      //         console.log(response);
      //     }
      // });
    });
  });
})(jQuery);
