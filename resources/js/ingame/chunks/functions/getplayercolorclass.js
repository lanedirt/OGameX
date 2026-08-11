function getPlayerColorClass(player) {
  switch (true) {
    case player.isAdmin:
      return "status_abbr_admin";

    case player.isBanned:
      return "status_abbr_banned";

    case player.isOnVacation:
      return "status_abbr_vacation";

    case player.isLongInactive:
      return "status_abbr_longinactive";

    case player.isInactive:
      return "status_abbr_inactive";

    case player.isOutlaw:
      return "status_abbr_outlaw";

    case player.isNewbie:
      return "status_abbr_noob";

    case player.isStrong:
      return "status_abbr_strong";

    case player.isHonorableTarget:
      return "status_abbr_honorableTarget";
  }

  return "status_abbr_active";
}
