function removeNotification(notifyBoxId) {
  $("notification-container notification#" + notifyBoxId).remove();
  clearTimeout(NotificationTimers[notifyBoxId].timer1);
  clearTimeout(NotificationTimers[notifyBoxId].timer2);
  clearTimeout(NotificationTimers[notifyBoxId].timer3);
  delete NotificationTimers[notifyBoxId];
}
