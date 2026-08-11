/**
 * loads an external js script and calls a function when it is loaded
 * @param url url of the script to load
 * @param callback function to call when script is loaded
 * @url http://www.nczonline.net/blog/2009/07/28/the-best-way-to-load-external-javascript/
 */

function loadScript(url, callback) {
  if (typeof loadScript.loadedScripts == "undefined") {
    loadScript.loadedScripts = {};
  }

  if (typeof loadScript.loadedScripts[url] == "undefined") {
    loadScript.loadedScripts[url] = true;
    var script = document.createElement("script");
    script.type = "text/javascript";

    if (script.readyState) {
      //IE
      script.onreadystatechange = function () {
        if (script.readyState == "loaded" || script.readyState == "complete") {
          script.onreadystatechange = null;
          callback();
        }
      };
    } else {
      //Others
      script.onload = function () {
        callback();
      };
    }

    script.src = url;
    var head = document.getElementsByTagName("head")[0];
    head.appendChild(script);
  } else {
    callback();
  }
}
