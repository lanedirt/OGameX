(function ($) {
  $.fn.ogameLineChart = function (data) {
    if (this.length > 0) {
      let that = $(this[0]);
      let lineChart = that.data("ogameLineChart");

      if (lineChart == null) {
        lineChart = new OGameLineChart(that, data);
        $(this).data("ogameLineChart", lineChart);
        lineChart.init();
        lineChart.render();
      }

      return lineChart;
    }

    return null;
  };
})(jQuery);
