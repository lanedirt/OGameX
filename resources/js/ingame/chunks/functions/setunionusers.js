function setUnionUsers() {
  var unionUsers = "";
  $("#participantselect")
    .find("li")
    .each(function () {
      unionUsers += $(this).attr("ref") + ";";
    });
  unionUsers = unionUsers.substring(0, unionUsers.length - 1);
  $("#unionUsers").val(unionUsers);
}
