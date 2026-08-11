function addPercentageBarPlugin() {
  (function (jQ) {
    jQ.fn.percentageBar = function (options) {
      let percentageBarInstance = new PercentageBar(this, options);
      return this;
    };
  })(jQuery);
}
