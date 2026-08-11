function updateOverlayToken(tokenId, updateToken) {
  $("[data-overlay-token-id=" + tokenId + "]").data("overlay-token", updateToken);
  token = updateToken;
}
