function initializeUI() {
  pushButton.addEventListener("click", function () {
    pushButton.disabled = true;

    if (isSubscribed) {
      unsubscribeUser();
    } else {
      subscribeUser();
    }
  }); // Set the initial subscription value

  swRegistration.pushManager.getSubscription().then(function (subscription) {
    isSubscribed = !(subscription === null);
    updateSubscriptionOnServer(subscription); // if (isSubscribed) {
    //     console.log('User IS subscribed.');
    // } else {
    //     console.log('User is NOT subscribed.');
    // }

    updateBtn();
  });
}
