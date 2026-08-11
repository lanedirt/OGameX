function scrollToTopOfDialog(dialog) {
  $("html, body")
    .stop()
    .animate(
      {
        scrollTop: Math.max(0, dialog.offset().top - 300),
      },
      200,
    );
}
