function getResourcesFromHeader(resourceId) {
  let value = $("#resources_" + resourceId).data("raw");
  return parseInt(value);
}
