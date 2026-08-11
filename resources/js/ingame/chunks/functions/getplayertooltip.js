function getPlayerTooltip(galaxyContentObject) {
  let { player } = galaxyContentObject;
  let { actions } = player;
  let rankLink = "";

  if (actions.highscore.available) {
    rankLink = `<li class="rank">${actions.highscore.title}: <a href="${actions.highscore.link}">${actions.highscore.rank}</a></li>`;
  }

  let messageLink = "";

  if (actions.message.available) {
    if (!actions.message.disabledChatBar) {
      messageLink = `<li><a href="javascript:void(0)" class="sendMail js_openChat" data-playerId="${player.playerId}">${actions.message.title}</a></li>`;
    } else {
      messageLink = `<li><a href="${actions.message.link}" data-playerId="${player.playerId}">${actions.message.title}</a></li>`;
    }
  }

  let buddyLink = "";

  if (actions.buddies.available) {
    buddyLink = `
                <li><a href="javascript:void(0);" class="sendBuddyRequestLink" data-playerid="${actions.buddies.playerId}" data-playername="${actions.buddies.playerName}">${actions.buddies.title}</a></li>
            `;
  }

  // Support link for admins (TODO: Implement proper support contact when messaging system is ready)
  let supportLink = "";
  if (actions.support && actions.support.available) {
    supportLink = `
                <li>
                    <a style="margin-top: 4px;"
                    href="${actions.support.link}"
                    target="_blank" title="${actions.support.title}"
                    class="js_hideTipOnMobile no_decoration">
                        <span class="support_icon icon icon_mail" style="margin-top: 5px;"></span> &nbsp;
                        <div style="position:absolute; top: 32px;left:30px">${actions.support.title}</div>
                    </a>
                </li>
            `;
  }

  let ignoreLink = "";

  if (actions.ignore.available) {
    ignoreLink = `<li><a href="javascript:void(0);" class="ignorePlayerLink" data-playerid="${actions.ignore.playerId}" data-playername="${actions.ignore.playerName}">${actions.ignore.title}</a></li>`;
  }

  return `
        <div id="player${player.playerId}" style="display: none;"  class="htmlTooltip galaxyTooltip">
            <h1>${getPlayerSelectedLanguage(player)}<span>${player.playerName}</span></h1>
            <div class="splitLine"></div>
            <ul class="ListLinks">
                ${rankLink}
                ${messageLink}
                ${buddyLink}
                ${supportLink}
                ${ignoreLink}
            </ul>
        </div>
        `;
}
