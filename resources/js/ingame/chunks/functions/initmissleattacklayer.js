function initMissleAttackLayer() {
  $("#rocketattack").closest(".ui-dialog-content").dialog("option", "title", $("#rocketattack").data("title"));
  $("#rocketattack input#missileCount")
    .keyup(function () {
      checkIntInput($(this), 1, $(this).data("max"));
    })
    .change(function () {
      checkIntInput($(this), 1, $(this).data("max"));
    })
    .focus();
  $("#rocketattack #number").bind("click", function () {
    var $input = $("#rocketattack input#missileCount");

    if (parseInt($input.val()) != $input.data("max")) {
      $input.val($input.data("max"));
    } else {
      $input.val("1");
    }
  });
  $("#rocketattack #priority a").bind("click", function () {
    var $this = $(this);
    var $primaryTarget = $("#primaryTarget");
    $("#rocketattack #priority a").not($this).removeClass("active");

    if ($this.hasClass("active")) {
      $this.removeClass("active");
      $primaryTarget.val("");
      $("#noPriorityInfo").show();
    } else {
      $this.addClass("active");
      $primaryTarget.val($this.attr("ref"));
      $("#noPriorityInfo").hide();
    }
  });
  $("form#rocketForm").submit(function () {
    $.post($(this).attr("action"), $(this).serialize(), function (response) {
      if (response) {
        launchMissiles(response);
      }
    });
    return false;
  });

  function updateArrivalTime() {
    var $timer = $("#rocketattack #arrivalTime #timer");
    $timer.html(getFormatedDate(serverTime.getTime() + 1000 * $timer.data("duration"), "[d].[m].[y] [G]:[i]:[s]"));
  }

  timerHandler.appendCallback(updateArrivalTime);
  updateArrivalTime();
}
