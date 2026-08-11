
// Buddy system and ignore player handlers
// NOTE: The sendBuddyRequestLink handler is now defined in the blade templates
// (galaxy/index.blade.php and highscore/players_points.blade.php) to open a BBCode dialog.
// The old direct-send implementation has been removed to avoid conflicts.

$(document).on("click", ".ignorePlayerLink", function (e) {
  e.preventDefault();
  var playerId = $(this).data("playerid");

  // Create a form and submit it to redirect
  var form = $("<form>", {
    method: "POST",
    action: "/buddies/ignore",
  });

  form.append(
    $("<input>", {
      type: "hidden",
      name: "_token",
      value: $('meta[name="csrf-token"]').attr("content"),
    }),
  );

  form.append(
    $("<input>", {
      type: "hidden",
      name: "ignored_user_id",
      value: playerId,
    }),
  );

  $("body").append(form);
  form.submit();
});
