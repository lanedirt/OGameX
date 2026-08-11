function drawErrorbox(type, message, header, options, link, otherclass) {
  var otherclass = otherclass == undefined ? false : otherclass;
  var domobject = otherclass !== false ? $("." + otherclass) : $(".build-it_disabled");

  if (typeof link == "undefined" || link == "") {
    link = document.location.href;
  }

  domobject.click(function () {
    if (header !== undefined && options !== undefined && link !== undefined) {
      if (type == "notify") {
        errorBoxNotify(header, message, options.allOk, function () {
          window.location.href = link;
        });
      }

      if (type == "decision") {
        errorBoxDecision(header, message, options.allYes, options.allNo, function () {
          window.location.href = link;
        });
      }
    }

    if (type == "fadeBox") {
      fadeBox(message, true);
    }
  });
}
