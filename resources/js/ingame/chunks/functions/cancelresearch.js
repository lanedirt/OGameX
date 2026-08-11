function cancelResearch(id, listId, question) {
  errorBoxDecision(LOCA_ALL_NETWORK_ATTENTION, "" + question + "", LOCA_ALL_YES, LOCA_ALL_NO, function () {
    window.location.replace(urlResearchCancel + "&techid=" + id + "&listid=" + listId);
  });
}
