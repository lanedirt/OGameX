function unsubscribeUser() {
  swRegistration.pushManager
    .getSubscription()
    .then(function (subscription) {
      if (subscription) {
        return subscription.unsubscribe();
      }
    })
    ["catch"](function (error) {
      // console.log('Error unsubscribing', error);
    })
    .then(function () {
      updateSubscriptionOnServer(null); // console.log('User is unsubscribed.');

      isSubscribed = false;
      updateBtn();
    });
}
