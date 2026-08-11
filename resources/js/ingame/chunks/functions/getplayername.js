function getPlayerName(galaxyContentObject, systemData) {
  let { player } = galaxyContentObject;
  let playerName = "";

  if (player.rank && player.rank.hasRank) {
    playerName = `<span class="honorRank ${player.rank.rankClass} tooltip js_hideTipOnMobile" title="${player.rank.rankTitle}"></span>`;
  }

  if (player.playerId !== systemData.playerId) {
    playerName += `<span class="playerName tooltipRel tooltipClose tooltipRight js_hideTipOnMobile ${getPlayerColorClass(player)}"
           rel="player${player.playerId}">${player.playerName}${getPlayerTooltip(galaxyContentObject)}</span>`;
  } else {
    playerName += `<span class="${getPlayerColorClass(player)} ownPlayerRow">${player.playerName}</span>`;
  }

  playerName += getPlayerAbbreviations(player, galaxyContentObject);
  return playerName;
}
