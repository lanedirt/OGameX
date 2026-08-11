function setNewTokenData(newToken) {
  $('#jumpgateForm input[name="token"]').val(newToken);
  $('#jumpgateDefaultTargetSelectionForm input[name="token"]').val(newToken);
  token = newToken;
}
