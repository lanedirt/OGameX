class CountdownTimer {
  constructor(
    targetName,
    leftoverTime,
    reloadPage,
    countdownDoneFunction,
    primaryReloadViaWS,
    maxDigits = 2,
    countValue = -1,
  ) {
    // default config
    this.countValue = parseInt(countValue);
    this.timestamp = 0;
    this.maxDigits = parseInt(maxDigits); // with 2 seconds won't be show when time is > 1h

    this.delimiter = " ";
    this.approx = "";
    this.showunits = true;
    this.zerofill = false;
    this.startTime = new Date().getTime();
    this.startLeftoverTime = parseInt(leftoverTime);
    this.targetName = targetName;
    this.reloadPage = reloadPage;
    this.countdownDoneFunction = countdownDoneFunction;
    this.primaryReloadViaWS = primaryReloadViaWS;
    this.timer = timerHandler.appendCallback(this.updateCountdown.bind(this));
    this.updateCountdown();
  } // Getter

  get getCurrentTimestring() {
    return formatTimeWrapper(
      this.getLeftoverTime(),
      this.maxDigits,
      this.showunits,
      this.delimiter,
      this.zerofill,
      this.approx,
    );
  } // Method

  getLeftoverTime() {
    let currTime = new Date();
    return Math.round(this.startLeftoverTime + ((currTime.getTime() - this.startTime) * this.countValue) / 1000);
  }

  updateCountdown() {
    let timeLeftInSeconds = this.getLeftoverTime();

    if (timeLeftInSeconds > 0) {
      $(`time.${this.targetName}`).text(this.getCurrentTimestring);
    } else {
      $(`time.${this.targetName}`).text(LocalizationStrings.status.ready);

      if (typeof this.countdownDoneFunction == "function") {
        this.countdownDoneFunction();
      }

      if (
        this.reloadPage != null &&
        !isOverlayOpen() &&
        (!this.primaryReloadViaWS || (this.primaryReloadViaWS === true && ogame.frontendActions.connected !== true))
      ) {
        reload_page(this.reloadPage);
      }

      timerHandler.removeCallback(this.timer);
    }
  }
}
