/*
 * jQuery plugin: fieldSelection - v0.1.1 - last change: 2006-12-16
 * (c) 2006 Alex Brem <alex@0xab.cd> - http://blog.0xab.cd
 */
(function () {
  var fieldSelection = {
    getSelection: function () {
      var e = this.jquery ? this[0] : this;
      return (
        /* mozilla / dom 3.0 */
        (
          ("selectionStart" in e &&
            function () {
              var l = e.selectionEnd - e.selectionStart;
              return {
                start: e.selectionStart,
                end: e.selectionEnd,
                length: l,
                text: e.value.substr(e.selectionStart, l),
              };
            }) ||
          (document.selection &&
            function () {
              e.focus();
              var r = document.selection.createRange();

              if (r === null) {
                return {
                  start: 0,
                  end: e.value.length,
                  length: 0,
                };
              }

              var re = e.createTextRange();
              var rc = re.duplicate();
              re.moveToBookmark(r.getBookmark());
              rc.setEndPoint("EndToStart", re);
              return {
                start: rc.text.length,
                end: rc.text.length + r.text.length,
                length: r.text.length,
                text: r.text,
              };
            }) ||
          /* browser not supported */
          function () {
            return null;
          }
        )()
      );
    },
    setSelection: function () {
      var e = this.jquery ? this[0] : this;
      var args = arguments[0] || {};
      return (
        /* mozilla / dom 3.0 */
        (
          ("selectionStart" in e &&
            function () {
              var start = typeof args == "object" ? args.start : args;

              if (start != undefined) {
                e.selectionStart = start;
              }

              if (args.end != undefined) {
                e.selectionEnd = args.end;
              }

              e.focus();
              return this;
            }) ||
          (document.selection &&
            function () {
              e.focus();
              var r = document.selection.createRange();

              if (r === null) {
                return this;
              }

              var start = typeof args == "object" ? args.start : args;

              if (start != undefined) {
                r.moveStart("character", -e.value.length);
                r.moveStart("character", start);
                r.collapse();
              }

              if (args.end != undefined) {
                r.moveEnd("character", args.end - start);
              }

              r.select();
              return this;
            }) ||
          /* browser not supported */
          function () {
            e.focus();
            return jQuery(e);
          }
        )()
      );
    },
    replaceSelection: function () {
      var e = this.jquery ? this[0] : this;
      var text = arguments[0] || "";
      return (
        /* mozilla / dom 3.0 */
        (
          ("selectionStart" in e &&
            function () {
              e.value = e.value.substr(0, e.selectionStart) + text + e.value.substr(e.selectionEnd, e.value.length);
              return this;
            }) ||
          (document.selection &&
            function () {
              e.focus();
              document.selection.createRange().text = text;
              return this;
            }) ||
          /* browser not supported */
          function () {
            e.value += text;
            return jQuery(e);
          }
        )()
      );
    },
  };
  jQuery.each(fieldSelection, function (i) {
    jQuery.fn[i] = this;
  });
})();
