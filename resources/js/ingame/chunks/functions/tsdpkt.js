function tsdpkt(f) {
  var vz = "";

  if (f < 0) {
    vz = "-";
  }

  f = Math.abs(f);
  var r = f % 1000;

  while (f >= 1000) {
    var k1 = "";

    if (f % 1000 < 100) {
      k1 = "0";
    }

    if (f % 1000 < 10) {
      k1 = "00";
    }

    if (f % 1000 == 0) {
      k1 = "00";
    }

    f = Math.abs((f - (f % 1000)) / 1000);
    r = (f % 1000) + LocalizationStrings["thousandSeperator"] + k1 + r;
  }

  r = vz + r;
  return r;
}
