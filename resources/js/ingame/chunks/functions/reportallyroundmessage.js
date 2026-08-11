function reportAllyRoundMessage(_elem, _messageId, _senderId, _question) {
  elem = _elem;
  messageId = _messageId;
  senderId = _senderId;
  errorBoxDecision(
    LocalizationStrings.attention,
    _question,
    LocalizationStrings.yes,
    LocalizationStrings.no,
    reportMessageCallback,
  );
}
