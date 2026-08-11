$(document).ready(function () {
  let params = new URL(document.location).searchParams;
  let reportHash = params.get("reportHash");

  if (reportHash) {
    loadSpyReport(
      reportHash,
      $("fleet-content[data-participant-id=0][data-attack-type=2] .loadInfoParticipant"),
      true,
      2,
    );
  }
});
