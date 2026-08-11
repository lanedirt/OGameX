class SimpleCountdownTimer {
  constructor(targetName, leftoverTime, reloadPage, countdownDoneFunction) {
    // default config
    this.timestamp = 0;
    this.maxDigits = 2;
    this.delimiter = " ";
    this.approx = "";
    this.showunits = true;
    this.zerofill = false;
    this.startTime = new Date().getTime();
    this.startLeftoverTime = parseInt(leftoverTime);
    this.targetName = targetName;
    this.reloadPage = reloadPage;
    this.countdownDoneFunction = countdownDoneFunction;
    this.timer = timerHandler.appendCallback(this.updateCountdown.bind(this));
    this.updateCountdown();
  }

  get getTimer() {
    return this.timer;
  }

  get getCurrentTimestring() {
    return formatTimeWrapper(
      this.getLeftoverTime(),
      this.maxDigits,
      this.showunits,
      this.delimiter,
      this.zerofill,
      this.approx,
    );
  }

  getLeftoverTime() {
    let currTime = new Date();
    return Math.round(this.startLeftoverTime + ((currTime.getTime() - this.startTime) * -1) / 1000);
  }

  updateCountdown() {
    let timeLeftInSeconds = this.getLeftoverTime();

    if (timeLeftInSeconds > 0) {
      $(this.targetName).text(this.getCurrentTimestring);
    } else {
      $(this.targetName).text(LocalizationStrings.status.ready);

      if (typeof this.countdownDoneFunction == "function") {
        this.countdownDoneFunction();
      }

      if (this.reloadPage != null) {
        reload_page(this.reloadPage);
      }

      timerHandler.removeCallback(this.timer);
    }
  }
}
