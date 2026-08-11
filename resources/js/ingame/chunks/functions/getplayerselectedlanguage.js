function getPlayerSelectedLanguage(player) {
  if (!player.selectedLanguageIcon) {
    return "";
  }

  return ` <selected-language-icon style="background-image: url('${player.selectedLanguageIcon}');" ></selected-language-icon> `;
}
