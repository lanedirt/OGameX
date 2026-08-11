(function ($) {
  $.fn.ogameLoadingIndicator = function (data) {
    if (this.length > 0) {
      let that = $(this[0]);
      let loadingIndicator = that.data("ogameLoadingIndicator");

      if (loadingIndicator == null) {
        loadingIndicator = new OGameLoadingIndicator(that, data);
        $(this).data("ogameLoadingIndicator", loadingIndicator);
        loadingIndicator.init();
      }

      return loadingIndicator;
    }

    return null;
  };
})(jQuery);
