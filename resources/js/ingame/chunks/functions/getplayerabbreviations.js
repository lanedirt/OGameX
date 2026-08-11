function getPlayerAbbreviations(player) {
  let returnStatus = [];

  if (player.isAdmin) {
    returnStatus.push(
      `<span class="status_abbr_admin tooltip js_hideTipOnMobile" title="${loca.LOCA_GALAXY_LEGEND_ADMIN}">${loca.LOCA_GALAXY_PLAYER_STATUS_A}</span>`,
    );
  } else {
    if (player.isBanned) {
      returnStatus.push(
        `<span class="status_abbr_banned tooltip js_hideTipOnMobile" title="${loca.LOCA_GALAXY_LEGEND_BANNED}">${loca.LOCA_GALAXY_PLAYER_STATUS_G}</span>`,
      );
    }

    if (player.isOnVacation) {
      returnStatus.push(
        `<span class="status_abbr_vacation tooltip js_hideTipOnMobile" title="${loca.LOCA_STATION_JUMP_VACATION}">${loca.LOCA_GALAXY_PLAYER_STATUS_U}</span>`,
      );
    }

    if (player.isLongInactive) {
      returnStatus.push(
        `<span class="status_abbr_longinactive tooltip js_hideTipOnMobile" title="${loca.LOCA_GALAXY_LEGEND_TWENTYEIGHT_DAYS_INACTIVE}">${loca.LOCA_GALAXY_PLAYER_STATUS_I_LONG}</span>`,
      );
    } else if (player.isInactive) {
      returnStatus.push(
        `<span class="status_abbr_inactive tooltip js_hideTipOnMobile" title="${loca.LOCA_GALAXY_LEGEND_SEVEN_DAYS_INACTIVE}">${loca.LOCA_GALAXY_PLAYER_STATUS_I}</span>`,
      );
    }

    if (player.isOutlaw) {
      returnStatus.push(
        `<span class="status_abbr_outlaw tooltipHTML" title="${loca.LOCA_GALAXY_LEGEND_OUTLAW}|${loca.LOCA_OUTLAW_EXPLANATION}">${loca.LOCA_GALAXY_PLAYER_STATUS_OUTLAW}</span>`,
      );
    }

    if (player.isNewbie) {
      returnStatus.push(
        `<span class="status_abbr_noob tooltip js_hideTipOnMobile" title="${loca.LOCA_GALAXY_LEGEND_NOOB}">${loca.LOCA_GALAXY_PLAYER_STATUS_N}</span>`,
      );
    }

    if (player.isStrong) {
      returnStatus.push(
        `<span class="status_abbr_strong tooltip js_hideTipOnMobile" title="${loca.LOCA_GALAXY_LEGEND_STRONG_PLAYER}">${loca.LOCA_GALAXY_PLAYER_STATUS_S}</span>`,
      );
    }

    if (player.isHonorableTarget) {
      returnStatus.push(
        `<span class="status_abbr_honorableTarget tooltipHTML" title="${loca.LOCA_GALAXY_LEGEND_HONORABLE_TARGET}|${loca.LOCA_GALAXY_LEGEND_HONORABLE_TARGET_EXPLANATION}">${loca.LOCA_GALAXY_PLAYER_STATUS_EP}</span>`,
      );
    }
  }

  return returnStatus.length ? `<pre> (${returnStatus.join()})</pre>` : "";
}
