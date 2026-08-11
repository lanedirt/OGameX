function addUserToUnionByForm() {
  var user = $("#unionUserSearch").find('[name="addtogroup"]');
  var userName = user.val();
  var participant = $("#participantselect");

  if (participant.find('li[ref="' + userName + '"]').length == 0) {
    participant.append($(document.createElement("li")).attr("ref", userName).text(userName));
  }

  user.val("");
}
