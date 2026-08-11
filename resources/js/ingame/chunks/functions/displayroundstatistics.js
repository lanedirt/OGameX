function displayRoundStatistics(round) {
  let statistic = getRoundStatistic(round);
  Object.keys(statistic).map((statisticKey) => {
    $(`#${statisticKey}`).text(statistic[statisticKey]);
  });
}
