Function.prototype.clone = function () {
  var fct = this;

  var clone = function () {
    return fct.apply(this, arguments);
  };

  clone.prototype = fct.prototype;

  for (var property in fct) {
    if (fct.hasOwnProperty(property) && property !== "prototype") {
      clone[property] = fct[property];
    }
  }

  return clone;
};
