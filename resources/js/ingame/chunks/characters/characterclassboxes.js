function CharacterClassBoxes(params) {
  this.loca = params.loca;
  this.init();
}

CharacterClassBoxes.prototype.init = function () {
  $(document).on("click", ".characterclass.boxes .buttons .freeselect", this.onClickFreeSelect.bind(this));
  $(document).on("click", ".characterclass.boxes .buttons .darkmatter", this.onClickDarkMatter.bind(this));
  $(document).on("click", ".characterclass.boxes .buttons .deactivate", this.onClickDeactivate.bind(this));
  $(document).on("click", ".characterclass.boxes .buttons .nodarkmatter", this.onClickNoDarkMatter.bind(this));
  $(document).on("click", ".characterclass.boxes .buttons .classchangeitem", this.onClickClassChangeItem.bind(this));
};

CharacterClassBoxes.prototype.hasActiveSelection = function () {
  return $(".characterclass.box.selected").length > 0;
};

CharacterClassBoxes.prototype.executeActionWithRedirect = function (url) {
  let that = this;
  $.post(url).done(function (data) {
    var json = $.parseJSON(data);

    if (json.status === "success") {
      fadeBox(
        json.message,
        false,
        function () {
          window.location.replace(json.redirectUrl);
        },
        2000,
      );
    } else {
      that.displayErrors(json);
    }
  });
};

CharacterClassBoxes.prototype.onClickFreeSelect = function (e) {
  let that = this;
  let url = $(e.currentTarget).attr("rel");
  $.post(url).done(function (data) {
    var json = $.parseJSON(data);

    if (json.status === "success") {
      fadeBox(
        json.message,
        false,
        function () {
          window.location.replace(json.redirectUrl);
        },
        2000,
      );
    } else {
      that.displayErrors(json);
    }
  });
};

CharacterClassBoxes.prototype.onClickDarkMatter = function (e) {
  let that = this;
  let url = $(e.currentTarget).attr("rel");
  let characterClassBox = $(e.currentTarget).closest(".characterclass.box");
  let name = characterClassBox.data("characterClassName");
  let price = characterClassBox.data("characterClassPrice");
  let label = this.loca.LOCA_CHARACTER_CLASS_NOTE_ACTIVATE_WITH_DM.replace("#characterClassName#", name).replace(
    "#darkmatter#",
    tsdpkt(price),
  );
  errorBoxDecision(this.loca.LOCA_ALL_NOTICE, label, this.loca.LOCA_ALL_YES, this.loca.LOCA_ALL_NO, function () {
    that.executeActionWithRedirect(url);
  });
};

CharacterClassBoxes.prototype.onClickNoDarkMatter = function (e) {
  let that = this;
  let urlDarkMatter = $(e.currentTarget).attr("rel");
  errorBoxDecision(
    this.loca.LOCA_ALL_NOTICE,
    this.loca.LOCA_ALL_ERROR_LACKING_DM,
    this.loca.LOCA_ALL_YES,
    this.loca.LOCA_ALL_NO,
    redirectPremium,
  );
};

CharacterClassBoxes.prototype.onClickDeactivate = function (e) {
  let that = this;
  let url = $(e.currentTarget).attr("rel");
  let characterClassBox = $(e.currentTarget).closest(".characterclass.box");
  let name = characterClassBox.data("characterClassName");
  let label = this.loca.LOCA_CHARACTER_CLASS_NOTE_DEACTIVATE.replace("#characterClassName#", name);
  errorBoxDecision(this.loca.LOCA_ALL_NOTICE, label, this.loca.LOCA_ALL_YES, this.loca.LOCA_ALL_NO, function () {
    that.executeActionWithRedirect(url);
  });
};

CharacterClassBoxes.prototype.onClickClassChangeItem = function (e) {
  let that = this;
  let url = $(e.currentTarget).attr("rel");
  let characterClassBox = $(e.currentTarget).closest(".characterclass.box");
  let name = characterClassBox.data("characterClassName");
  let label = this.loca.LOCA_CHARACTER_CLASS_NOTE_ACTIVATE_WITH_ITEM.replace("#characterClassName#", name);
  errorBoxDecision(this.loca.LOCA_ALL_NOTICE, label, this.loca.LOCA_ALL_YES, this.loca.LOCA_ALL_NO, function () {
    let params = {
      _token: token,
    };
    $.post(url, params).done(function (data) {
      var json = $.parseJSON(data);
      token = json.newAjaxToken;

      if (json.status === "success") {
        window.location.reload();
      } else {
        that.displayErrors(json);
      }
    });
  });
};

CharacterClassBoxes.prototype.displayErrors = function (data) {
  let errorCode = data.errorCode || 0;
  let errorMessage = data.errorMessage || data.message || "";
  fadeBox(errorMessage, true);
};
