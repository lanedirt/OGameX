function jumpgateDefaultTargetSelectionCallback(data) {
  var data = $.parseJSON(data);

  if (data["status"]) {
    token = data.token;
    $("#jumpgateForm").find('input[name="token"]').val(data.token);
    var targetSelect = $("#jumpgateForm").find('select[name="targetSpaceObjectId"]');
    targetSelect.find("option").removeAttr("selected");
    var optionNode = targetSelect.find('option[value="' + data["targetMoon"] + '"]');

    if (optionNode.length) {
      optionNode.attr("selected", "selected");
    } else {
      if (targetSelect.find('option[value="0"]').length == 0) {
        targetSelect.append(
          $(document.createElement("option")).attr("value", 0).attr("selected", "selected").text("--"),
        );
      } else {
        targetSelect.find('option[value="0"]').attr("selected", "selected");
      }
    }

    targetSelect.trigger("change"); // not sure if the following is enough to refresh that dropdown

    targetSelect.ogameDropDown("refresh");
  }

  errorBoxAsArray(data["errorbox"]);

  if (typeof data.newAjaxToken != "undefined") {
    setNewTokenData(data.newAjaxToken);
  }
}
