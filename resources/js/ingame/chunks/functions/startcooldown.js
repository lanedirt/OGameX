function startCooldown($durationEl, $pusherEl, maxHeight) {
  if ($.trim($durationEl.text()).match(/^\d+$/)) {
    var countdown = new countdownWithTickFunction(
      $durationEl[0],
      parseInt($.trim($durationEl.text())),
      parseInt($durationEl.attr("data-total-duration")),
      function () {
        location.href = getRedirectLink(); // reload, damit das item verschwindet
      },
      function (duration, totalDuration) {
        var faktor = 1 - duration / totalDuration;
        var realHeight = Math.floor(maxHeight * faktor);
        $pusherEl.css("height", realHeight + "px");
      },
    );
  }
}
