/* rounds end */
/**
 * convert seconds to time interval for display. Usage in formatTime
 * @param timestamp Time interval in seconds
 * @param maxDigits count of shown digits
 * @param showUnits flag to use localized units
 * @param delimiter used delimiter
 * @param zerofill flag to use two digit zerofill
 * @param approx prefix for time string
 * @returns {string}
 */
function formatTimeWrapper(timestamp, maxDigits, showUnits, delimiter, zerofill, approx) {
  let timeUnits = {
    week: 604800,
    day: 86400,
    hour: 3600,
    minute: 60,
    second: 1,
  };
  let timeString = "";

  for (let k in timeUnits) {
    let nv = Math.floor(timestamp / timeUnits[k]);

    if (maxDigits > 0 && (nv > 0 || (zerofill && timeString !== ""))) {
      timestamp = timestamp - nv * timeUnits[k];

      if (timeString !== "") {
        timeString += delimiter;

        if (nv < 10 && nv > 0 && zerofill) {
          nv = "0" + nv;
        }

        if (nv === 0) {
          nv = "00";
        }
      }

      timeString += nv + (showUnits ? LocalizationStrings.timeunits["short"][k] : "");
      maxDigits--;
    }
  }

  if (timestamp > 0) {
    timeString = approx + timeString;
  }

  return timeString;
}
