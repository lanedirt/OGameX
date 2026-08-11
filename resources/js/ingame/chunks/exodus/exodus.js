function Exodus(cfg) {
  this.loca = cfg.loca;
  this.initServerSelect(cfg);
}

Exodus.prototype.onAjaxDone = function () {
  this.loadingIndicator.hide(); //@todo handle error
};

Exodus.prototype.onAjaxError = function () {};

Exodus.prototype.updateToken = function (updatetoken) {
  this.token = updatetoken;
  token = updatetoken;
};

Exodus.prototype.initCommon = function (cfg) {
  this.serverWrapper = $(".exodus #serverNumbers");
  this.loadingIndicator = this.serverWrapper.ogameLoadingIndicator();
  this.serverLanguage = $(".exodus #serverLanguage");
  this.serverNumber = $(".exodus #serverNumber");
  this.serverNumbers = null;
  this.token = cfg.token;
  this.selectedLanguage = cfg.sourceLanguage;
  this.serverSelection = $("#serverSelection");
  this.table = this.serverLanguage.closest(".og-table");
  this.availableLanguages = cfg.availableLanguages;
  this.urlServerIds = cfg.urlServerIds || null;
  this.urlIsReady = cfg.urlIsReady || null;
  this.urlSubmitExodus = cfg.urlSubmitExodus || null;
  this.isReadyForExodus = cfg.isReadyForExodus || false;
  this.serverAmount = 0;
};

Exodus.prototype.initServerSelect = function (cfg) {
  this.initCommon(cfg);
  this.dropDownLanguageSelect = $(".exodus #serverLanguage");
  this.dropDownServerSelect = $(".exodus #serverNumber");
  this.exodusSubmitButton = $(".exodus #submitExodus");
  this.exodusSubmitButton.on("click", this.onClickExecute.bind(this));
  this.dropDownLanguageSelect.on("change", this.onChangeServerLanguage.bind(this));
  this.dropDownServerSelect.on("change", this.onChangeServerNumber.bind(this));
  this.refreshLanguage();
  this.fetchServerNumbers();
};

Exodus.prototype.onChangeServerLanguage = function (e) {
  this.selectedLanguage = this.dropDownLanguageSelect.val();
  this.refreshLanguage();
  this.fetchServerNumbers();
};

Exodus.prototype.onChangeServerNumber = function (e) {
  this.selectedServer = this.dropDownServerSelect.val();
  this.refreshServer();
};

Exodus.prototype.onClickExecute = function (e) {
  e.stopPropagation();
  e.preventDefault();
  let serverName = this.dropDownServerSelect.find('option[value="' + this.selectedServer + '"]').html();
  this.loadingIndicator.show();
  let that = this;
  errorBoxDecision(
    this.loca.LOCA_EXODUS_TRANSFER_QUESTION_TITLE,
    this.loca.LOCA_EXODUS_TRANSFER_QUESTION.replace("#uniname#", serverName),
    this.loca.LOCA_ALL_YES,
    this.loca.LOCA_ALL_NO,
    function () {
      let data = {
        serverNumber: parseInt(that.selectedServer),
        serverLanguage: that.selectedLanguage,
        _token: that.token,
      };
      $.post(that.urlSubmitExodus, data, that.handleSubmitExodusResponse.bind(that)).done(that.onAjaxDone.bind(that));
    },
    function () {
      that.loadingIndicator.hide();
    },
  );
};

Exodus.prototype.fetchServerNumbers = function () {
  this.loadingIndicator.show();
  let data = {
    language: this.selectedLanguage,
  };
  $.getJSON(this.urlServerIds, data, this.onFetchServerNumbers.bind(this)).done(this.onAjaxDone.bind(this));
};

Exodus.prototype.checkIsReady = function () {
  let data = {};

  if (this.serverAmount > 0) {
    $.getJSON(this.urlIsReady, data, this.onCheckIsReady.bind(this)).done(this.onAjaxDone.bind(this));
  }
};

Exodus.prototype.setLanguage = function (language) {
  let languageOld = this.selectedLanguage;
  this.selectedLanguage = language;

  if (this.selectedLanguage !== languageOld) {
    this.resetLanguage();
    this.refreshLanguage();
    this.fetchServerNumbers();
    this.resetServer();
    this.checkIsReady();
  }
};

Exodus.prototype.setServer = function (number) {
  let serverOld = this.selectedServer;
  this.selectedServer = number;

  if (this.selectedServer !== serverOld) {
    this.resetServer();
    this.refreshServer();
    this.checkIsReady();
  }
};

Exodus.prototype.refreshLanguage = function () {
  this.dropDownLanguageSelect.find("option").removeAttr("selected");

  if (this.selectedLanguage) {
    this.dropDownLanguageSelect.find('option[value="' + this.selectedLanguage + '"]').attr("selected", "selected");
    this.dropDownLanguageSelect.val(this.selectedLanguage);
  }
};

Exodus.prototype.refreshServer = function () {
  this.dropDownServerSelect.find("option").removeAttr("selected");

  if (this.selectedServer) {
    this.dropDownServerSelect.val(this.selectedServer);
    this.dropDownServerSelect.find('option[value="' + this.selectedServer + '"]').attr("selected", "selected");
  }
};

Exodus.prototype.resetLanguage = function () {
  this.setLanguage(this.getFirstLanguage());
};

Exodus.prototype.resetServer = function () {
  this.setServer(this.selectedServer);
};

Exodus.prototype.getFirstLanguage = function () {
  return this.availableLanguages[0];
};

Exodus.prototype.onCheckIsReady = function (data) {
  this.isReadyForExodus = data.isReady;

  if (this.isReadyForExodus && this.selectedServer !== 0) {
    this.exodusSubmitButton.removeClass("disabled");
  } else {
    this.exodusSubmitButton.addClass("disabled");
  }
};

Exodus.prototype.handleSubmitExodusResponse = function (response) {
  let data = JSON.parse(response);
  let status = data.status || "failure";

  if (status === "success") {
    fadeBox(data.message, false);
    window.location = data.redirectUrl;
  } else {
    this.updateToken(data.newAjaxToken);
    this.displayErrors(data);
  }
};

Exodus.prototype.displayErrors = function (data) {
  // only display the first error
  let error = data.errors[0] || undefined;

  if (error) {
    fadeBox(error.message, true);
    this.updateToken(data.newAjaxToken);
  }
};

Exodus.prototype.onFetchServerNumbers = function (data) {
  if (data.serverNumbers.length === 0 || this.isReadyForExodus === false) {
    if (this.isReadyForExodus === false) {
      this.serverSelection.html('<span id="noServer">' + this.loca.LOCA_EXODUS_RESTRICTION_NOT_FULFILLED + "</span>");
    }

    if (data.serverNumbers.length === 0) {
      this.serverSelection.html('<span id="noServer">' + this.loca.LOCA_EXODUS_NO_SERVER_AVAILABLE + "</span>");
    }

    this.exodusSubmitButton.addClass("disabled");
    this.serverAmount = 0;
    this.selectedServer = 0;
    return;
  } else {
    this.serverSelection.html('<select id="serverNumber" type="text" class="og-input" name="serverNumber"></select>');
    this.exodusSubmitButton.removeClass("disabled");
    this.dropDownServerSelect = $(".exodus #serverNumber");
    this.dropDownServerSelect.on("change", this.onChangeServerNumber.bind(this));
    this.serverAmount = data.serverNumbers.length;
  }

  this.selectedServer = data.serverNumbers[0].number;
  this.serverNumbers = data.serverNumbers;
  var select = this.dropDownServerSelect;

  if (select.prop) {
    var options = select.prop("options");
  } else {
    var options = select.attr("options");
  }

  $("option", select).remove();
  let htmlOptions = "";
  $.each(data.serverNumbers, function (key, server) {
    options[options.length] = new Option(server.name, server.number);
    htmlOptions += '<option value="' + server.number + '">' + server.name + "</option>";
  });
  this.dropDownServerSelect.ogameDropDown("destroy");
  this.dropDownServerSelect.html("");
  this.dropDownServerSelect.html(htmlOptions).ogameDropDown();
};
