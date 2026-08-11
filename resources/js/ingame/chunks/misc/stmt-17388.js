ogame.buddies = {
  initBuddyList: function () {
    $.tablesorter.addParser({
      id: "coordinates",
      is: function (s) {
        return false;
      },
      format: function (s) {
        // format your data for normalization
        var res = $.trim(s).slice(1, -1).split(":");
        var result = res[0] * 16384 + res[1] * 32 + res[2] * 1;
        return result;
      },
      type: "numeric",
    });
    $.tablesorter.addParser({
      id: "commaDigit",
      is: function (s, table) {
        var c = table.config;
        return jQuery.tablesorter.isDigit(s.replace(/\./g, ""), c);
      },
      format: function (s) {
        return jQuery.tablesorter.formatFloat(s.replace(/\./g, ""));
      },
      type: "numeric",
    });
    $(".content_table").tablesorter({
      widgets: ["zebra"],
      headers: {
        0: {
          sorter: false,
        },
        2: {
          sorter: "commaDigit",
        },
        5: {
          sorter: "coordinates",
        },
        6: {
          sorter: false,
        },
      },
      cssHeader: "ct_sortable_title",
      cssAsc: "ct_sort_asc",
      cssDesc: "ct_sort_desc",
    });
    $("#buddylist").off(".buddyList");
    $("#buddylist").on("click.buddyList", ".deleteBuddy", deleteBuddy);
    $("#chatContent").on("click.chat_info", ".deleteBuddy", deleteBuddy);
  },

  /*
   * Initializes that is needed for the buddies page
   * @returns {undefined}
   */
  initBuddies: function () {
    // adding hover style for tables
    $(".zebra tr")
      .mouseover(function () {
        $(this).addClass("over");
      })
      .mouseout(function () {
        $(this).removeClass("over");
      }); // toggles the buddy requests section

    $(".js_accordion").accordion({
      collapsible: true,
      heightStyle: "content",
    }); // for buddy requests

    $(".js_tabs").tabs();
    $(".js_scrollbar").mCustomScrollbar({
      theme: "ogame",
    });
  },
};
