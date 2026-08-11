function updateSubscriptionOnServer(subscription) {
  if (subscription) {
    $.post("?page=ajax&component=subscription&action=subscribe", {
      subscription: JSON.stringify(subscription),
    });
  }
}
