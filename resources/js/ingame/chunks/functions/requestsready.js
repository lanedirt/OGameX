function requestsReady() {
  $(document).on("click", ".acceptRequest", acceptRequest);
  $(document).on("click", ".rejectRequest", rejectRequest);
  $(document).on("click", ".cancelRequest", cancelRequest);
  $(document).on("click", ".reportRequest", reportRequest);
}
