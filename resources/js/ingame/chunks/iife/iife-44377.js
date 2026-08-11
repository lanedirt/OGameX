(function ($) {
  $.fn.ogamePaginatable = function (data) {
    if (this.length > 0) {
      let that = $(this[0]);
      let localData = data || {};
      let paginatable = that.data("ogamePaginatable");

      if (paginatable == null) {
        paginatable = new OGamePaginatable(that, localData);
        $(this).data("ogamePaginatable", paginatable);
        paginatable.init();
      }

      return paginatable;
    }

    return null;
  };
})(jQuery);
