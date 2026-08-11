function Graveyard(cfg) {
  this.loca = cfg.loca;
  this.initServerSelect(cfg);
}

Graveyard.prototype.onAjaxDone = function () {
  this.loadingIndicator.hide(); //@todo handle error
};

Graveyard.prototype.onAjaxError = function () {};

Graveyard.prototype.updateToken = function (token) {
  this.token = token;
};

Graveyard.prototype.initCommon = function (cfg) {
  this.serverWrapper = $(".graveyard #serverNumbers");
  this.loadingIndicator = this.serverWrapper.ogameLoadingIndicator();
  this.serverLanguage = $(".graveyard #serverLanguage");
  this.serverNumber = $(".graveyard #serverNumber");
  this.serverNumbers = null;
  this.selectedLanguage = null;
  this.serverSelection = $("#serverSelection");
  this.token = cfg.token;
  this.table = this.serverLanguage.closest(".og-table");
  this.availableLanguages = cfg.availableLanguages;
  this.urlServerIds = cfg.urlServerIds || null;
  this.urlIsReady = cfg.urlIsReady || null;
  this.urlSubmitExodus = cfg.urlSubmitExodus || null;
  this.isReadyForExodus = cfg.isReadyForExodus || false;
  this.serverAmount = 0;
};

Graveyard.prototype.initServerSelect = function (cfg) {
  this.initCommon(cfg);
  this.dropDownLanguageSelect = $(".graveyard #serverLanguage");
  this.dropDownServerSelect = $(".graveyard #serverNumber");
  this.exodusSubmitButton = $(".graveyard #submitExodus");
  this.exodusSubmitButton.on("click", this.onClickExecute.bind(this));
  this.dropDownLanguageSelect.on("change", this.onChangeServerLanguage.bind(this));
  this.dropDownServerSelect.on("change", this.onChangeServerNumber.bind(this));
  this.resetLanguage();
  this.refreshLanguage();
};

Graveyard.prototype.onChangeServerLanguage = function (e) {
  this.selectedLanguage = this.dropDownLanguageSelect.val();
  this.refreshLanguage();
  this.fetchServerNumbers();
};

Graveyard.prototype.onChangeServerNumber = function (e) {
  this.selectedServer = this.dropDownServerSelect.val();
  this.refreshServer();
};

Graveyard.prototype.onClickExecute = function (e) {
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

Graveyard.prototype.fetchServerNumbers = function () {
  this.loadingIndicator.show();
  let data = {
    language: this.selectedLanguage,
  };
  $.getJSON(this.urlServerIds, data, this.onFetchServerNumbers.bind(this)).done(this.onAjaxDone.bind(this));
};

Graveyard.prototype.checkIsReady = function () {
  let data = {};

  if (this.serverAmount > 0) {
    $.getJSON(this.urlIsReady, data, this.onCheckIsReady.bind(this)).done(this.onAjaxDone.bind(this));
  }
};

Graveyard.prototype.setLanguage = function (language) {
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

Graveyard.prototype.setServer = function (number) {
  let serverOld = this.selectedServer;
  this.selectedServer = number;

  if (this.selectedServer !== serverOld) {
    this.resetServer();
    this.refreshServer();
    this.checkIsReady();
  }
};

Graveyard.prototype.refreshLanguage = function () {
  this.dropDownLanguageSelect.find("option").removeAttr("selected");

  if (this.selectedLanguage) {
    this.dropDownLanguageSelect.find('option[value="' + this.selectedLanguage + '"]').attr("selected", "selected");
    this.dropDownLanguageSelect.val(this.selectedLanguage);
  }
};

Graveyard.prototype.refreshServer = function () {
  this.dropDownServerSelect.find("option").removeAttr("selected");

  if (this.selectedServer) {
    this.dropDownServerSelect.find('option[value="' + this.selectedServer + '"]').attr("selected", "selected");
  }
};

Graveyard.prototype.resetLanguage = function () {
  this.setLanguage(this.getFirstLanguage());
};

Graveyard.prototype.resetServer = function () {
  this.setServer(this.selectedServer);
};

Graveyard.prototype.getFirstLanguage = function () {
  return this.availableLanguages[0];
};

Graveyard.prototype.onCheckIsReady = function (data) {
  this.isReadyForExodus = data.isReady;

  if (this.isReadyForExodus && this.selectedServer !== 0) {
    this.exodusSubmitButton.removeClass("disabled");
  } else {
    this.exodusSubmitButton.addClass("disabled");
  }
};

Graveyard.prototype.handleSubmitExodusResponse = function (response) {
  let data = JSON.parse(response);
  let status = data.status || "failure";

  if (status === "success") {
    fadeBox(data.message, false);
    window.location = data.redirectUrl;
  } else {
    this.displayErrors(data);
  }
};

Graveyard.prototype.displayErrors = function (data) {
  // only display the first error
  let error = data.errors[0] || undefined;

  if (error) {
    this.updateToken(data.newAjaxToken);
    fadeBox(error.message, true);
  }
};

Graveyard.prototype.onFetchServerNumbers = function (data) {
  if (data.serverNumbers.length === 0) {
    if (data.serverNumbers.length === 0) {
      this.serverSelection.html('<span id="noServer">' + this.loca.LOCA_EXODUS_NO_SERVER_AVAILABLE + "</span>");
    }

    if (this.isReadyForExodus === false) {
      this.serverSelection.html('<span id="noServer">' + this.loca.LOCA_EXODUS_RESTRICTION_NOT_FULFILLED + "</span>");
    }

    this.exodusSubmitButton.addClass("disabled");
    this.serverAmount = 0;
    this.selectedServer = 0;
    return;
  } else {
    this.serverSelection.html('<select id="serverNumber" type="text" class="og-input" name="serverNumber"></select>');
    this.exodusSubmitButton.removeClass("disabled");
    this.dropDownServerSelect = $(".graveyard #serverNumber");
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
