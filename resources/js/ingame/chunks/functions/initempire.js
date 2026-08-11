function initEmpire() {
  initConnectionErrorFunction();
  $(".secondCat").each(function () {
    $(this).find("li:last").addClass("catbox-end");
  });
  $(".values").each(function () {
    if (!$(this).hasClass("groupitems")) {
      $(this).find("div:even").addClass("even");
      $(this).find("div:odd").addClass("odd");
      $(this).find("div:last").addClass("box-end");
    }

    if ($(this).children().hasClass("equipment")) {
      $(this).children(".equipment").addClass("box-end");
    }
  });
  $("#settings li:last").addClass("set-end");
  $(".header h3").hover(
    function () {
      $(this).addClass($(this).attr("class") + "hover");
    },
    function () {
      $(this).removeClass("openhover").removeClass("closehover");
    },
  );
  $(".header h3").click(function () {
    $(this).removeClass("openhover").removeClass("closehover");
    var actualClass = $(this).attr("class");

    if (actualClass == "open") {
      $(this).addClass("close");
    } else {
      $(this).addClass("open");
    }

    $(this).removeClass(actualClass);
  });
  $(".planet").hover(
    function () {
      $(this).addClass("move");
    },
    function () {
      $(this).removeClass("move");
    },
  );
  $(".values div img").hover(
    function () {
      $(this).addClass("imghover");
    },
    function () {
      $(this).removeClass("imghover");
    },
  );
  $("#planetsTab").click(function () {
    window.location.href = empireUrl + "&planetType=0";
  });

  if (moonCount > 0) {
    $("#moonsTab").click(function () {
      window.location.href = empireUrl + "&planetType=1";
    });
  } else {
    $("#moonsTab").addClass("nomoons");
  }

  if (planetType == 1) {
    $("#planetsTab").removeClass("active");
    $("#moonsTab").addClass("active");
  }

  initBuffBarEmpire();
}
