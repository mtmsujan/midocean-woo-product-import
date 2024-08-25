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
      colorInputRow.find(".stock-value").text("100"); // Reset stock value
      colorInputRow.find(".color-name").text("Red -"); // Reset the color name
      colorInputRow.find(".color-preview circle").attr("fill", ""); // Reset the color preview
      colorInputRow.appendTo(".color-input-container"); // Append the cloned row inside the container

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
      button.find(".color-preview circle").attr("fill", selectedColor); // Update the SVG color
      button.find(".color-name").text(selectedColorName); // Update the color name

      // Optional: Make API call based on selected color (this can be done via AJAX)
      // $.ajax({
      //     url: 'your-api-url',
      //     method: 'POST',
      //     data: { color: selectedColor },
      //     success: function(response) {
      //         console.log(response);
      //     }
      // });
    });

    // Event delegation to remove color row when 'x' is clicked
    $(document).on("click", ".close-button", function () {
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
    }
  });
})(jQuery);

document.addEventListener("alpine:init", () => {

  let initColor = () => {
    return JSON.parse(JSON.stringify({
      name: "Red",
      value: "#f00",  
      stock: 200,
      id: "color_1"
    }))
  }
  Alpine.data("colors", function() {
    return {
      
      replaceColor(newColor, colorId){
        let color = this.selectedColors.find(color => color.id === colorId)
        let index = this.selectedColors.indexOf(color)
        this.selectedColors.splice(index, 1, newColor)
      },
      selected(colorId){
        let color = this.selectedColors.find(color => color.id === colorId)
        return color
      },
      get isColorAvailable() {
        return this.selectedColors.length < this.colors.length
      },
      colors: [
        initColor(),
        {
          name: "Green",
          value: "green",
          stock: 200,
          id: "color_" + new Date().getTime()
        },
      ],
      selectedColors:[
        initColor()
      ],
      addColor() {
        let newColor = this.colors.filter(color => {
          return this.selectedColors.some(selectedColor => {
            return color.id !== selectedColor.id
          })
        }).shift()
        
        if (newColor) {
          this.selectedColors.push(newColor);
        }
      },
      calculateTotal(value, currentStock) {
        value = parseInt(value)
        let subtotal = value * currentStock
        console.log(subtotal)
      },
      
    };
  });


});
