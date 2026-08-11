/**
 * adds prefix digits to a number ('2'->'02')
 *
 * @param int   number
 * @param int   digits
 * @param str   prefix, default is '0'
 */

function dezInt(num, size, prefix) {
  prefix = prefix ? prefix : "0";
  var minus = num < 0 ? "-" : "",
    result = prefix == "0" ? minus : "";
  num = Math.abs(parseInt(num, 10));
  size -= ("" + num).length;

  for (var i = 1; i <= size; i++) {
    result += "" + prefix;
  }

  result += (prefix != "0" ? minus : "") + num;
  return result;
}
