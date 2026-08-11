(function ($) {
  $.fn.ogameSortable = function (data) {
    if (this.length > 0) {
      let that = $(this[0]);
      let localData = data || {};
      let sortable = that.data("ogameSortable");

      if (sortable == null) {
        sortable = new OGameSortable(that, localData);
        $(this).data("ogameSortable", sortable);
        sortable.init();
      }

      return sortable;
    }

    return null;
  };
})(jQuery);
