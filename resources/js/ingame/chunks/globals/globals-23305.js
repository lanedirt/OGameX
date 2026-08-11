var Formatter = {
  // #################################################################################################################
  // ##  DATE, TIME & PERIOD  ########################################################################################
  // #################################################################################################################

  /**
   * Human-readable time units
   */
  timeUnits: {
    second: 1,
    minute: 60,
    hour: 60 * 60,
    day: 24 * 60 * 60,
    week: 7 * 24 * 60 * 60,
  },

  /**
   * Format seconds to human readable time (weeks, days, hours, minutes, seconds)
   *
   * @param {int} seconds
   * @param {Array} options
   * @returns {string}
   */
  secondsToTime: function (seconds, options) {
    var defaults = {
      segments: -1,
      delimiter: " ",
      units: {
        weeks: "w",
        days: "d",
        hours: "h",
        minutes: "m",
        seconds: "s",
      },
      now: "now",
    };
    options = Object.assign(defaults, options);

    if (!Number.isInteger(seconds)) {
      throw "Integer expected for parameter seconds";
    }

    if (seconds <= 0) {
      return options.now;
    }

    var time = {
      weeks: Math.floor(seconds / Formatter.timeUnits.week),
      days: Math.floor((seconds % Formatter.timeUnits.week) / Formatter.timeUnits.day),
      hours: Math.floor((seconds % Formatter.timeUnits.day) / Formatter.timeUnits.hour),
      minutes: Math.floor((seconds % Formatter.timeUnits.hour) / Formatter.timeUnits.minute),
      seconds: seconds % Formatter.timeUnits.minute,
    };
    var formattedTime = [];

    for (var segment in time) {
      if (time[segment] > 0 && (options.segments == -1 || formattedTime.length < options.segments)) {
        formattedTime.push(time[segment] + options.units[segment]);
      }
    }

    return formattedTime.join(options.delimiter);
  },

  /**
   * Format seconds to period string (PDTHMS)
   *
   * @param {int} seconds
   * @returns {string}
   */
  secondsToPeriod: function (seconds) {
    if (!Number.isInteger(seconds)) {
      throw "Integer expected for parameter seconds";
    }

    if (seconds <= 0) {
      return "PT0H0M0S";
    }

    var period = {
      days: {
        value: Math.floor(seconds / Formatter.timeUnits.day),
        unit: "D",
      },
      hours: {
        value: Math.floor((seconds % Formatter.timeUnits.day) / Formatter.timeUnits.hour),
        unit: "H",
      },
      minutes: {
        value: Math.floor((seconds % Formatter.timeUnits.hour) / Formatter.timeUnits.minute),
        unit: "M",
      },
      seconds: {
        value: seconds % Formatter.timeUnits.minute,
        unit: "S",
      },
    };
    var formattedPeriodDays = [],
      formattedPeriodTime = [];

    for (var segment in period) {
      if (period[segment].value > 0) {
        if (segment == "days") {
          formattedPeriodDays.push(period[segment].value + period[segment].unit);
        } else {
          formattedPeriodTime.push(period[segment].value + period[segment].unit);
        }
      }
    }

    return (
      "P" + formattedPeriodDays.join("") + (formattedPeriodTime.length > 0 ? "T" + formattedPeriodTime.join("") : "")
    );
  },
  // #################################################################################################################
  // ##  NUMBER ######################################################################################################
  // #################################################################################################################

  /**
   * Format number to string
   *
   * @param {number} number
   * @param {Array} options
   * @returns {string}
   */
  numberToString: function (number, options) {
    var NUMBER_PRECISION_MINIMUM = 0;
    var NUMBER_PRECISION_MAXIMUM = 3;
    var NUMBER_TRANSFORM_ROUND = 1;
    var NUMBER_TRANSFORM_CEIL = 2;
    var NUMBER_TRANSFORM_FLOOR = 3;
    /**
     * Round fractions up or down with precision
     *
     * @param {number} number
     * @param {int} precision
     * @returns {number}
     */

    var round = function (number, precision) {
      if (typeof number !== "number") {
        throw "Numeric expected for parameter number";
      }

      if (!Number.isInteger(precision)) {
        throw "Integer expected for parameter precision";
      }

      if (precision <= 0) {
        return Math.round(number);
      }

      return Math.round(number * Math.pow(10, precision)) / Math.pow(10, precision);
    };
    /**
     * Round fractions up with precision
     *
     * @param {number} number
     * @param {int} precision
     * @returns {number}
     */

    var ceil = function (number, precision) {
      if (typeof number !== "number") {
        throw "Numeric expected for parameter number";
      }

      if (!Number.isInteger(precision)) {
        throw "Integer expected for parameter precision";
      }

      if (precision <= 0) {
        return Math.ceil(number);
      }

      return Math.ceil(number * Math.pow(10, precision)) / Math.pow(10, precision);
    };
    /**
     * Round fractions down with precision
     *
     * @param {number} number
     * @param {int} precision
     * @returns {number}
     */

    var floor = function (number, precision) {
      if (typeof number !== "number") {
        throw "Numeric expected for parameter number";
      }

      if (!Number.isInteger(precision)) {
        throw "Integer expected for parameter precision";
      }

      if (precision <= 0) {
        return Math.floor(number);
      }

      return Math.floor(number * Math.pow(10, precision)) / Math.pow(10, precision);
    };
    /**
     * Transform number using round, ceil or floor
     *
     * @param {number} number
     * @param {int} mode
     * @param {int} precision
     * @returns {number}
     */

    var transformNumber = function (number, mode, precision) {
      switch (mode) {
        case NUMBER_TRANSFORM_ROUND:
          number = round(number, precision);
          break;

        case NUMBER_TRANSFORM_CEIL:
          number = ceil(number, precision);
          break;

        case NUMBER_TRANSFORM_FLOOR:
          number = floor(number, precision);
          break;
      }

      return number;
    };

    var defaults = {
      transform: NUMBER_TRANSFORM_ROUND,
      precision: {
        minimum: NUMBER_PRECISION_MINIMUM,
        maximum: NUMBER_PRECISION_MAXIMUM,
      },
      separators: {
        thousands: ",",
        decimals: ".",
      },
      boundaries: {
        /*1000:           "K",*/
        1000000: "M",
        1000000000: "Bn",
      },
    };
    options = Object.assign(defaults, options);

    if (typeof number !== "number") {
      throw "Numeric expected for parameter number";
    }

    if (typeof options.precision === "undefined" || typeof options.precision.minimum === "undefined") {
      throw "Minimum precision not specified";
    }

    if (typeof options.precision === "undefined" || typeof options.precision.maximum === "undefined") {
      throw "Maximum precision not specified";
    }

    if (options.precision.minimum > options.precision.maximum) {
      throw "Minimum precision larger than maximum precision";
    }

    var boundaries = Object.keys(options.boundaries).sort(function (a, b) {
      return parseFloat(b) - parseFloat(a);
    });
    var unit = "";

    for (var i = 0; i < boundaries.length; i++) {
      var boundary = parseInt(boundaries[i]);

      if (boundary === 0) {
        continue;
      }

      if (Math.abs(number) >= boundary) {
        number /= boundary;
        unit = options.boundaries[boundaries[i]];
        break;
      }
    }

    var precision = options.precision.maximum;

    while (
      (number = transformNumber(number, options.transform, precision)) ==
        transformNumber(number, options.transform, precision - 1) &&
      precision > options.precision.minimum
    ) {
      precision--;
    }

    number = number.toFixed(precision);
    var numberParts = number.toString().split(".");
    numberParts[0] = numberParts[0].replace(/\B(?=(\d{3})+(?!\d))/g, options.separators.thousands);
    return numberParts.join(options.separators.decimals) + unit;
  },
};
