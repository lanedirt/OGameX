// this seems to only get used in the showmessage.tpl.php which is only called on galaxy.inc.tpl.php and jumpgate.tpl.php
// @TODO is it depreceated???

function initShowMessage() {
  var $dialog = $('.overlayDiv[data-page="showmessage"]');
  $(".answerHeadline", $dialog).click(function () {
    $(this).toggleClass("open");

    if ($(this).hasClass("open")) {
      $(".answerForm", $dialog).show();
      $(".textWrapper", $dialog).addClass("textWrapperSmall");
      $(".textWrapper", $dialog).removeClass("textWrapper");
    } else {
      $(".answerForm", $dialog).hide();
      $(".textWrapperSmall", $dialog).addClass("textWrapper");
      $(".textWrapperSmall", $dialog).removeClass("textWrapperSmall");
    }
  });
  $(".note > div:first-child", $dialog).addClass("newMessage");
  $(".info:odd", $dialog).css("margin-left", "40px");
  $("div.note p:first").after('<span class="seperator">');
  $(".answerHeadline", $dialog).hover(
    function () {
      $(this).addClass("pushable");
    },
    function () {
      $(this).removeClass("pushable");
    },
  );
  $(".melden", $dialog).click(function () {
    manageErrorbox($(this).attr("rel"), 1);
  });
}
