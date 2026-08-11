class CountdownTimerUnit {
  constructor(targetName, leftoverTime, shipCount, targetTechnologyId, timePerUnit, reloadPage, maxDigits = 2) {
    // default config
    this.timestamp = 0;
    this.maxDigits = parseInt(maxDigits); // with 2 seconds won't be show when time is > 1h

    this.delimiter = " ";
    this.approx = "";
    this.showunits = true;
    this.zerofill = false;
    this.startTime = new Date().getTime();
    this.startLeftoverTime = parseInt(leftoverTime);
    this.shipCount = shipCount;
    this.timePerUnit = timePerUnit;
    this.targetTechnologyId = targetTechnologyId;
    this.targetName = targetName;
    this.reloadPage = reloadPage;
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
    return Math.round(this.startLeftoverTime + ((currTime.getTime() - this.startTime) * -1) / 1000);
  }

  updateCountdown() {
    let timeLeftInSeconds = this.getLeftoverTime();
    let currentShipCount = parseInt(
      $(`li.technology[data-technology=${this.targetTechnologyId}] span.amount`).attr("data-value"),
    );
    let factor = Math.max(0, timeLeftInSeconds) / this.timePerUnit;
    $(`time.${this.targetName}`).text(this.getCurrentTimestring);

    if (factor > 0) {
      $(`li.technology[data-technology=${this.targetTechnologyId}][data-status="active"] .cooldownBackground`).css(
        "height",
        factor * 100 + "%",
      );
    } else {
      // let targetAmount = $(`li.technology[data-technology=${this.targetTechnologyId}] span.targetamount`).data('value')
      if (this.shipCount > 0) {
        this.shipCount--; // targetAmount--

        currentShipCount++;
      }

      if (this.shipCount >= 0) {
        // $(`li.technology[data-technology=${this.targetTechnologyId}] span.targetamount`).attr('data-value', targetAmount).text(targetAmount)
        $(`.shipSumCount.${this.targetName}`).text(this.shipCount);
      }

      $(`li.technology[data-technology=${this.targetTechnologyId}] span.amount`).attr("data-value", currentShipCount);
      $(`li.technology[data-technology=${this.targetTechnologyId}] span.amount .stockAmount`).text(
        gfNumberGetHumanReadable(currentShipCount),
      );

      if (this.shipCount > 0) {
        this.startTime = new Date().getTime();
        this.startLeftoverTime = this.timePerUnit; // new CountdownTimerUnit(this.targetName, this.leftoverTime, this.shipCount, this.targetTechnologyId, this.timePerUnit, this.reloadPage)

        $(`time.${this.targetName}`).text(LocalizationStrings.status.ready);
      } else {
        if (timeLeftInSeconds <= -1 && timeLeftInSeconds > -6) {
          if (this.reloadPage != null && !isOverlayOpen()) {
            reload_page(this.reloadPage);
          }

          timerHandler.removeCallback(this.timer);
        }

        $(`time.${this.targetName}`).text(LocalizationStrings.status.ready);
      }
    }
  }
}
