function clampInt(val, minVal, maxVal, allowEmpty) {
  if (allowEmpty && (val === "" || val === 0)) {
    return "";
  }

  let intVal = parseInt(val);

  if (isNaN(intVal)) {
    return minVal;
  }

  intVal = Math.min(intVal, maxVal);
  intVal = Math.max(intVal, minVal);
  return intVal;
}
