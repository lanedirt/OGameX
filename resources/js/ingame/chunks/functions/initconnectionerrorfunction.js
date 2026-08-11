function initConnectionErrorFunction() {
  if (isMobile) {
    document.addEventListener(
      "deviceready",
      function () {
        $(document).ajaxError(function (e, xhr, settings, exception) {
          HostApp.ShowNoConnectionScreen();
        });
      },
      false,
    );
  }
}
