/**
 * Object.assign polyfill
 * @see https://developer.mozilla.org/de/docs/Web/JavaScript/Reference/Global_Objects/Object/assign#Polyfill
 */
if (typeof Object.assign !== "function") {
  Object.assign = function (target) {
    if (target == null) {
      throw new TypeError("Cannot convert undefined or null to object");
    }

    target = Object(target);

    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i];

      if (source != null) {
        for (var key in source) {
          if (Object.prototype.hasOwnProperty.call(source, key)) {
            target[key] = source[key];
          }
        }
      }
    }

    return target;
  };
}
