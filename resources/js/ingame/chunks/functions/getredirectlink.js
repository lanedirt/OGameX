function getRedirectLink(params) {
  var finalParams = {};

  if (params != undefined) {
    for (var key in params) {
      finalParams[key] = params[key];
    }

    return $.param.fragment($.param.querystring(window.location.href, finalParams), {}); //Return witch anchor and added params
  } else {
    return window.location.href.split("#")[0]; //return url without anchor
  }
}
