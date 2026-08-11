// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2011 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// Html tags
// http://en.wikipedia.org/wiki/html
// ----------------------------------------------------------------------------
// Basic set. Feel free to add more tags
// ----------------------------------------------------------------------------

/**
 * Ogame specific initialisation code for the markItUp Editor
 *
 * @param locaKeys { Object }
 * @param itemArray { Object }
 * @param imagesAllowed { Boolean }
 * @param selector { String } - is specified, only init editor for this selector
 * @param maxChars { Number } - maximum length of entered text
 * @param lite { bool } - limits possible functions
 **/
function initBBCodeEditor(locaKeys, itemArray, imagesAllowed, selector, maxChars, lite) {
  var $textareas;

  if (typeof selector == "undefined" || selector == null) {
    $textareas = $("textarea:not(.markItUpEditor)");
  } else {
    $textareas = $(selector).filter(":not(.markItUpEditor)");
  }

  if ($textareas.length == 0) {
    return;
  }

  var imagesAllowed = imagesAllowed || false;
  var fontSizeArray = [];

  for (var fontSizeCounter = 6; fontSizeCounter <= 30; fontSizeCounter += 2) {
    fontSizeArray.push({
      name: fontSizeCounter,
      openWith: "[size=" + fontSizeCounter + "]",
      closeWith: "[/size]",
      placeHolder: "Text",
      className: "fontSize" + fontSizeCounter,
    });
  }

  var itemDropdownArray = [];
  $.each(itemArray, function (uuid, name) {
    itemDropdownArray.push({
      name: name,
      replaceWith: "[item]" + uuid + "[/item]",
    });
  });

  function multiInsertSelectionFix(e) {
    if (e.selection.length == 0) {
      return;
    }

    var $textarea = $(e.textarea);
    var range = $textarea.getSelection();
    var listItemStart = $textarea.val().indexOf(e.openWith, range.start);
    var rangeValue = listItemStart + e.openWith.length;
    var placeHolderLength = typeof e.placeHolder == "string" ? e.placeHolder.length : 0;
    var newRange = {
      start: rangeValue,
      end: rangeValue + placeHolderLength,
    };
    $textarea.setSelection(newRange);
  }

  function colorPicker(e) {
    var openWith = e.openWith;
    var $textarea = $(e.textarea);
    var selection = $textarea.getSelection();
    $textarea
      .siblings(".colorpicker")
      .val("")
      .colorpicker("open")
      .colorpicker("option", "close", function (e, color) {
        var replaceBy = openWith.replace(/%colorCode%/, color.formatted); // determine new text selection

        var selectionOffset = "%colorCode%".length - color.formatted.length;
        selection.start = selection.start - selectionOffset;
        selection.end = selection.end - selectionOffset;
        $textarea.val($textarea.val().replace(openWith, replaceBy)).setSelection(selection);
      });
    var $button = $textarea.siblings(".markItUpHeader").find("." + e.className);
    $(".ui-colorpicker:visible")
      .css("top", $button.offset().top + $button.height())
      .css("left", $button.offset().left);
    $(".ui-colorpicker").draggable();
    $(".ui-colorpicker-rgb-r .ui-colorpicker-number").focus();
    return e;
  }

  function backgroundImage(e) {
    var $textarea = $(e.textarea);
    var selection = $textarea.getSelection();
    var openWith = e.openWith;
    var $imagePickerOverlay = $("#backgroundImagePicker");

    if ($imagePickerOverlay.is(":visible")) {
      $imagePickerOverlay.dialog("close");
    }

    $imagePickerOverlay.find("input:checked").prop("checked", false);
    $imagePickerOverlay.find(".url").val("http://").focus();
    openOverlay($imagePickerOverlay, {
      type: "inline",
      title: locaKeys.backgroundImage,
      close: function () {
        var imageText = $imagePickerOverlay.find(".url").val();
        var checkedX = $imagePickerOverlay.find(".repeatX:checked").length;
        var checkedY = $imagePickerOverlay.find(".repeatY:checked").length;
        var repeat = "";

        if (checkedX && checkedY) {
          repeat = "yes";
        } else if (checkedX) {
          repeat = "yes-x";
        } else if (checkedY) {
          repeat = "yes-y";
        }

        if (repeat.length) {
          imageText += " image-repeat=" + repeat;
        }

        var replaceBy = openWith.replace(/%image%/, imageText); // determine new text selection

        var selectionOffset = "%image%".length - imageText.length;
        selection.start = selection.start - selectionOffset;
        selection.end = selection.end - selectionOffset;
        $textarea.val($textarea.val().replace(openWith, replaceBy)).setSelection(selection);
      },
    });
    var $button = $textarea.siblings(".markItUpHeader").find("." + e.className);
    $imagePickerOverlay
      .parent()
      .css("top", $button.offset().top + $button.height())
      .css("left", $button.offset().left);
    return e;
  } // END function backgroundImage(e)

  var markupSetBasic = lite
    ? [
        {
          name: locaKeys.bold,
          key: "B",
          openWith: "[b]",
          closeWith: "[/b]",
          className: "bold",
        },
        {
          name: locaKeys.italic,
          key: "I",
          openWith: "[i]",
          closeWith: "[/i]",
          className: "italic",
        },
        {
          name: locaKeys.fontColor,
          afterInsert: colorPicker,
          openWith: "[color=%colorCode%]",
          closeWith: "[/color]",
          placeHolder: locaKeys.textPlaceHolder,
          className: "fontColor",
        },
        {
          name: locaKeys.fontSize,
          className: "fontSize",
          dropMenu: fontSizeArray,
        },
        {
          name: locaKeys.list,
          openWith: "[*]",
          multiline: true,
          openBlockWith: "[list]\n",
          closeBlockWith: "\n[/list]",
          className: "list",
          afterMultiInsert: multiInsertSelectionFix,
          placeHolder: locaKeys.textPlaceHolder,
        },
        {
          name: locaKeys.coordinates,
          openWith: "[coordinates]",
          closeWith: "[/coordinates]",
          placeHolder: locaKeys.coordinatePlaceHolder,
          className: "coordinates",
        },
      ]
    : [
        {
          name: locaKeys.bold,
          key: "B",
          openWith: "[b]",
          closeWith: "[/b]",
          className: "bold",
        },
        {
          name: locaKeys.italic,
          key: "I",
          openWith: "[i]",
          closeWith: "[/i]",
          className: "italic",
        },
        {
          name: locaKeys.fontColor,
          afterInsert: colorPicker,
          openWith: "[color=%colorCode%]",
          closeWith: "[/color]",
          placeHolder: locaKeys.textPlaceHolder,
          className: "fontColor",
        },
        {
          name: locaKeys.fontSize,
          className: "fontSize",
          dropMenu: fontSizeArray,
        },
        {
          name: locaKeys.list,
          openWith: "[*]",
          multiline: true,
          openBlockWith: "[list]\n",
          closeBlockWith: "\n[/list]",
          className: "list",
          afterMultiInsert: multiInsertSelectionFix,
          placeHolder: locaKeys.textPlaceHolder,
        },
        {
          name: locaKeys.link,
          key: "L",
          openWith: "[url=[![" + locaKeys.link + ":!:http://]!]]",
          closeWith: "[/url]",
          placeHolder: locaKeys.textPlaceHolder,
          className: "link",
        },
        {
          name: locaKeys.coordinates,
          openWith: "[coordinates]",
          closeWith: "[/coordinates]",
          placeHolder: locaKeys.coordinatePlaceHolder,
          className: "coordinates",
        },
      ];
  var markupSetAdvanced = lite
    ? [
        {
          name: locaKeys.underline,
          key: "U",
          openWith: "[u]",
          closeWith: "[/u]",
          className: "underline",
        },
        {
          name: locaKeys.stroke,
          key: "S",
          openWith: "[s]",
          closeWith: "[/s]",
          className: "strikeThrough",
        },
        {
          name: locaKeys.sub,
          openWith: "[sub]",
          closeWith: "[/sub]",
          className: "sub",
        },
        {
          name: locaKeys.sup,
          openWith: "[sup]",
          closeWith: "[/sup]",
          className: "sup",
        },
        {
          separator: "-",
        },
        {
          name: locaKeys.item,
          className: "item",
          dropMenu: itemDropdownArray,
        },
        {
          name: locaKeys.player,
          openWith: "[player]",
          closeWith: "[/player]",
          placeHolder: locaKeys.playerPlaceHolder,
          className: "player",
        },
        {
          separator: "-",
        },
        {
          name: locaKeys.alignLeft,
          openWith: "[align=left]",
          closeWith: "[/align]",
          className: "leftAlign",
        },
        {
          name: locaKeys.alignCenter,
          openWith: "[align=center]",
          closeWith: "[/align]",
          className: "centerAlign",
        },
        {
          name: locaKeys.alignRight,
          openWith: "[align=right]",
          closeWith: "[/align]",
          className: "rightAlign",
        },
        {
          name: locaKeys.alignJustify,
          openWith: "[align=justify]",
          closeWith: "[/align]",
          className: "justifyAlign",
        },
        {
          separator: "-",
        },
        {
          name: locaKeys.code,
          openWith: "[code]",
          closeWith: "[/code]",
          className: "code",
        },
        {
          separator: "-",
        },
        {
          name: locaKeys.email,
          key: "E",
          openWith: "[email=[![" + locaKeys.email + ":!:]!]]",
          closeWith: "[/email]",
          placeHolder: locaKeys.textPlaceHolder,
          className: "email",
        },
        {
          name: locaKeys.preview,
          className: "preview",
          call: "preview",
        },
      ]
    : [
        {
          name: locaKeys.underline,
          key: "U",
          openWith: "[u]",
          closeWith: "[/u]",
          className: "underline",
        },
        {
          name: locaKeys.stroke,
          key: "S",
          openWith: "[s]",
          closeWith: "[/s]",
          className: "strikeThrough",
        },
        {
          name: locaKeys.sub,
          openWith: "[sub]",
          closeWith: "[/sub]",
          className: "sub",
        },
        {
          name: locaKeys.sup,
          openWith: "[sup]",
          closeWith: "[/sup]",
          className: "sup",
        },
        {
          name: locaKeys.backgroundColor,
          afterInsert: colorPicker,
          openWith: "[background color=%colorCode%]",
          closeWith: "[/background]",
          placeHolder: locaKeys.textPlaceHolder,
          className: "backgroundColor",
        },
        {
          name: locaKeys.backgroundImage,
          afterInsert: backgroundImage,
          openWith: "[background image=%image%]",
          closeWith: "[/background]",
          placeHolder: locaKeys.textPlaceHolder,
          className: "backgroundImage",
        },
        {
          separator: "-",
        },
        {
          name: locaKeys.item,
          className: "item",
          dropMenu: itemDropdownArray,
        },
        {
          name: locaKeys.player,
          openWith: "[player]",
          closeWith: "[/player]",
          placeHolder: locaKeys.playerPlaceHolder,
          className: "player",
        },
        {
          name: locaKeys.tooltip,
          openWith: '[tooltip position="top" text="[![Tooltip Text:!:Tooltip Text]!]"]',
          closeWith: "[/tooltip]",
          placeHolder: locaKeys.textPlaceHolder,
          className: "tooltip",
        },
        {
          separator: "-",
        },
        {
          name: locaKeys.alignLeft,
          openWith: "[align=left]",
          closeWith: "[/align]",
          className: "leftAlign",
        },
        {
          name: locaKeys.alignCenter,
          openWith: "[align=center]",
          closeWith: "[/align]",
          className: "centerAlign",
        },
        {
          name: locaKeys.alignRight,
          openWith: "[align=right]",
          closeWith: "[/align]",
          className: "rightAlign",
        },
        {
          name: locaKeys.alignJustify,
          openWith: "[align=justify]",
          closeWith: "[/align]",
          className: "justifyAlign",
        },
        {
          separator: "-",
        },
        {
          name: locaKeys.block,
          openWith: "[p]",
          closeWith: "[/p]",
          className: "block",
        },
        {
          name: locaKeys.code,
          openWith: "[code]",
          closeWith: "[/code]",
          className: "code",
        },
        {
          name: locaKeys.spoiler,
          openWith: "[spoiler]",
          closeWith: "[/spoiler]",
          className: "spoiler",
        },
        {
          separator: "-",
        },
        {
          name: locaKeys.hr,
          openWith: "[hr]",
          className: "hr",
        },
        {
          separator: "-",
        },
        {
          name: locaKeys.picture,
          key: "Z",
          replaceWith: "[img][![" + locaKeys.picture + ":!:http://]!][/img]",
          className: "picture",
        },
        {
          name: locaKeys.email,
          key: "E",
          openWith: "[email=[![" + locaKeys.email + ":!:]!]]",
          closeWith: "[/email]",
          placeHolder: locaKeys.textPlaceHolder,
          className: "email",
        },
        {
          name: locaKeys.preview,
          className: "preview",
          call: "preview",
        },
      ];

  if (!imagesAllowed) {
    $.each(markupSetAdvanced, function (index) {
      if (this.className == "picture" || this.className == "backgroundImage") {
        markupSetAdvanced.splice(index, 1);
      }
    });
  }

  if (isMobile) {
    // dont show colorpickers on ipad
    $.each(markupSetBasic, function (index) {
      if (this.className == "fontColor" || this.className == "backgroundColor") {
        markupSetBasic.splice(index, 1);
      }
    });
    $.each(markupSetAdvanced, function (index) {
      if (this.className == "fontColor" || this.className == "backgroundColor") {
        markupSetAdvanced.splice(index, 1);
      }
    });
  }

  var editorSettings = {
    onShiftEnter: {
      keepDefault: false,
      replaceWith: "\n",
    },
    onCtrlEnter: {
      keepDefault: false,
      openWith: "\n[p]",
      closeWith: "[/p]",
    },
    onTab: {
      keepDefault: false,
      replaceWith: "\t",
    },
    markupSet: [markupSetBasic, markupSetAdvanced],
    resizeHandle: false,
    previewParserPath: bbcodePreviewUrl ? bbcodePreviewUrl + "&imgAllowed=" + (imagesAllowed ? 1 : 0) : "",
    previewAutoRefresh: true,
    previewParserVar: "text",
    previewInElement: $('<div class="miu_preview_container"></div>'),
    afterInsert: function (e) {
      $(e.textarea).trigger("keyup");
    },
  };
  $.colorpicker.regional["custom"] = locaKeys.colorPicker;
  $textareas.each(function () {
    var $this = $(this);
    $this
      .markItUp(editorSettings) // create colorpicker
      .after(
        $('<input type="hidden" class="colorpicker"/>').colorpicker({
          color: "#000000",
          colorFormat: "#HEX",
          hsv: false,
          parts: "popup",
          regional: "custom",
          showCancelButton: false,
        }),
      ); // The preview element needs to be inserted after creation of editor to be in the right place

    editorSettings.previewInElement.insertAfter($this); // preview action as link and not as set button

    $(".miu_advanced .preview").hide(); // create a footer for char count and preview link

    var $miuFooter = $('<div class="miu_footer clearfix"></div>');
    $miuFooter
      .append('<a role="button" class="fright txt_link btn_blue preview_link">' + locaKeys.preview + "</a>")
      .append('<span class="fleft"><span class="cnt_chars">' + maxChars + "</span> " + locaKeys.charsLeft + "</span>");
    $miuFooter.insertAfter($this);
    $this.on("keyup.bbCodeEditor", function () {
      if ($this.val().length > maxChars) {
        $this.val($this.val().substr(0, maxChars));
      }

      $this
        .closest(".markItUpContainer")
        .find(".cnt_chars")
        .html(maxChars - $this.val().length);
    });
    $(".miu_preview_container").hide();
    $(".preview_link").on("click.bbCodeEditor", function () {
      editorSettings.previewInElement.insertAfter($(event.target).closest(".markItUpContainer").find(".miu_footer"));
      $(event.target).closest(".markItUpContainer").find(".miu_preview_container").show();
      $(event.target).closest(".markItUpContainer").find(".preview").click();
    });

    if (isMobile) {
      $this.siblings(".markItUpHeader").find("a").attr("title", "");
      $this
        .siblings(".markItUpHeader")
        .find("li:not(.markItUpDropMenu) a")
        .bind("mouseup", function () {
          // open keyboard again
          $this.focus();
        });
    } else {
      $this.siblings(".markItUpHeader").find("ul ul a").attr("title", "");
    }

    if ($("#backgroundImagePicker").length == 0) {
      $("body").append(
        $(
          '<div id="backgroundImagePicker" style="display: none;"><table>' +
            "<tr><td>" +
            locaKeys.backgroundImage +
            ':</td><td><input type="text" class="url"/></td></tr>' +
            "<tr><td>" +
            locaKeys.backgroundImagePicker.repeatX +
            ':</td><td><input type="checkbox" class="repeatX"/></td></tr>' +
            "<tr><td>" +
            locaKeys.backgroundImagePicker.repeatY +
            ':</td><td><input type="checkbox" class="repeatY"/></td></tr>' +
            "</table>" +
            '<div><a href="javascript:void(0);" class="btn_blue">' +
            locaKeys.backgroundImagePicker.ok +
            "</a></div>" +
            "</div>",
        ),
      );
      $("#backgroundImagePicker")
        .find("a")
        .bind("click", function () {
          $("#backgroundImagePicker").dialog("close");
        });
    }
  });
  /* *** adding additional event listeners *** */

  $(".toggle_miu_advanced").on("click.bbCodeEditor", function () {
    if ($(".miu_advanced").is(":visible")) {
      $(".miu_advanced").hide();
      $(this).removeClass("hide_miu_advanced").addClass("show_miu_advanced");
    } else {
      $(".miu_advanced").show();
      $(this).removeClass("show_miu_advanced").addClass("hide_miu_advanced");
    }
  });
}
