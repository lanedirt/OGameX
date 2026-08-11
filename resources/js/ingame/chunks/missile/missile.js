function Missile(cfg) {
  this.token = cfg.token;
  this.interceptor = cfg.interceptor;
  this.maxInterceptor = cfg.maxInterceptor;
  this.interplanetary = cfg.interplanetary;
  this.maxInterplanetary = cfg.maxInterplanetary;
  this.urlMissileDestroy = cfg.urlMissileDestroy || null;
  this.initCommon(cfg);
}

Missile.prototype.onAjaxDone = function (response) {
  let data = JSON.parse(response);

  if (data.status === "success") {
    $("#rocketsilo").parents(".overlayDiv").dialog("close");
    let technologyId = $("#technologydetails").data("technology-id");
    technologyDetails.show(technologyId);
    this.deattachCommon();
    $(".rocketlayer").remove();
  }
};

Missile.prototype.initCommon = function (cfg) {
  if ($(".rocketlayer").length > 1) {
    for (let i = 0; i < $(".rocketlayer").length - 1; i++) {
      $($(".rocketlayer")[i]).remove();
    }
  }

  this.missileInterceptor = $("#rocketsilo #destroy_" + this.interceptor);
  this.missileInterplanetary = $("#rocketsilo #destroy_" + this.interplanetary);
  this.destroyButton = $("#rocketsilo #destroyMissiles");
  this.deattachCommon();
  this.missileInterceptor.on("focus", this.onFocusInterceptor.bind(this));
  this.missileInterceptor.on("keyup", this.onKeyInputInterceptor.bind(this));
  this.missileInterplanetary.on("focus", this.onFocusInterplanetary.bind(this));
  this.missileInterplanetary.on("keyup", this.onKeyInputInterplanetary.bind(this));
  this.destroyButton.on("click", this.onClickButtonDestroy.bind(this));
};

Missile.prototype.deattachCommon = function () {
  this.missileInterceptor.off("focus");
  this.missileInterceptor.off("keyup");
  this.missileInterplanetary.off("focus");
  this.missileInterplanetary.off("keyup");
  this.destroyButton.off("click");
};

Missile.prototype.onFocusInterceptor = function (e) {
  this.missileInterceptor.val("");
};

Missile.prototype.onKeyInputInterceptor = function (e) {
  var value = $(e.target).val();

  if (typeof value !== undefined && value !== "") {
    intVal = Math.abs(getValue(value));
    intVal = Math.min(intVal, this.maxInterceptor);
    $(e.target).val(intVal);
  }
};

Missile.prototype.onFocusInterplanetary = function (e) {
  this.missileInterplanetary.val("");
};

Missile.prototype.onKeyInputInterplanetary = function (e) {
  var value = $(e.target).val();

  if (typeof value !== undefined && value !== "") {
    intVal = Math.abs(getValue(value));
    intVal = Math.min(intVal, this.maxInterplanetary);
    $(e.target).val(intVal);
  }
};

Missile.prototype.onClickButtonDestroy = function (e) {
  e.preventDefault();
  let data = {
    interceptorMissile: this.missileInterceptor.val() || 0,
    interplanetaryMissile: this.missileInterplanetary.val() || 0,
    _token: this.token,
  };
  $.post(this.urlMissileDestroy, data, this.onDestroyMissiles.bind(this)).done(this.onAjaxDone.bind(this));
};

Missile.prototype.onDestroyMissiles = function (response) {
  let data = JSON.parse(response);
  this.token = data.newAjaxToken;
  token = data.newAjaxToken;

  if (data.status === "success") {
    fadeBox(data.message, false);
  } else {
    fadeBox(data.message, true);
  }
};
