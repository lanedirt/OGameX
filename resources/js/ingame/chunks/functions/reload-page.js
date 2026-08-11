/**
 * counter finished reload page
 * @param url
 */

function reload_page(url) {
  if (timerHandler && !timerHandler.pageReloadAlreadyTriggered) {
    timerHandler.pageReloadAlreadyTriggered = true;
    openParentLocation(url);
  }
}
