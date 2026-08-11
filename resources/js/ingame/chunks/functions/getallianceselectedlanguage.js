function getAllianceSelectedLanguage(player) {
  if (!player.allianceSelectedLanguage) {
    return "";
  }

  return ` <selected-language-icon style="background-image: url('${player.allianceSelectedLanguage}');" ></selected-language-icon> `;
}
