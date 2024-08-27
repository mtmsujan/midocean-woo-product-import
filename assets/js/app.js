(function ($) {
  $(document).ready(function () {
    // product additional information accordion
    $("#additional-information-accordion").accordion({
      collapsible: true,
      active: false,
    });

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

document.addEventListener("alpine:init", () => {
  Alpine.data("quantityChecker", () => ({
    quantity: null, // Start with an empty string for the quantity
    get hasQty() {
      return this.quantity && this.quantity > 0;
    }
  }));
});
