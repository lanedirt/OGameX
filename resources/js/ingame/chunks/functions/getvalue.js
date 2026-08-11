function getValue(value) {
  result = parseInt(
    value
      .toString()
      .replace(/^k$/, "1000")
      .replace(/k/, "000")
      .replace(/^0+/, "")
      .replace(/[^0-9]/g, ""),
  );
  return isNaN(result) ? 0 : result;
}
