jQuery.fn.slideFadeToggle = function (speed, easing, callback) {
  return this.animate(
    {
      opacity: "toggle",
      width: "toggle",
    },
    speed,
    easing,
    callback,
  );
};
