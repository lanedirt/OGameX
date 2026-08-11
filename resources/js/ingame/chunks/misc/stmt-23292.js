/**
 * Number.isInteger polyfill
 * @see https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Number/isInteger#Polyfill
 */

if (typeof Number.isInteger !== "function") {
  //noinspection JSPrimitiveTypeWrapperUsage
  Number.isInteger = function (number) {
    return (
      typeof number === "number" &&
      isFinite(number) &&
      number > -9007199254740992 &&
      number < 9007199254740992 &&
      Math.floor(number) === number
    );
  };
}
