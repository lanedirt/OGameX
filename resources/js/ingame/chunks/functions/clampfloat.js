function clampFloat(val, minVal, maxVal) {
  let floatVal = parseFloat(val);

  if (isNaN(floatVal)) {
    return minVal;
  }

  floatVal = Math.max(floatVal, minVal);
  floatVal = Math.min(floatVal, maxVal);
  return floatVal;
}
