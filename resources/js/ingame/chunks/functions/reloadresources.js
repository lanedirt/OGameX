function reloadResources(data, callback) {
  if (typeof data == "string") {
    data = $.parseJSON(data);
  }

  resourcesBar.reload(data);
  resourcesBar.activateOnClick();

  if (data.vacation === true) {
    resourcesBar.stop();
  } else {
    resourcesBar.restart();
  }

  honorScore = data.honorScore;
  darkMatter = data.resources.darkmatter.amount;

  if (typeof callback == "function") {
    callback(data.resources);
  }
}
