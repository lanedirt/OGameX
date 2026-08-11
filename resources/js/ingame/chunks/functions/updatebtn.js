function updateBtn() {
  if (Notification.permission === "denied") {
    pushButton.textContent = "Push Messaging Blocked.";
    pushButton.disabled = true;
    updateSubscriptionOnServer(null);
    return;
  }

  if (isSubscribed) {
    pushButton.textContent = "Disable Push Messaging";
    pushButton.checked = true;
  } else {
    pushButton.textContent = "Enable Push Messaging";
    pushButton.checked = false;
  }

  pushButton.disabled = false;
}
