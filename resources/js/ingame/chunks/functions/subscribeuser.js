function subscribeUser() {
  var applicationServerKey = urlB64ToUint8Array(applicationServerPublicKey);
  swRegistration.pushManager
    .subscribe({
      userVisibleOnly: true,
      applicationServerKey: applicationServerKey,
    })
    .then(function (subscription) {
      // console.log('User is subscribed.');
      updateSubscriptionOnServer(subscription);
      isSubscribed = true;
      updateBtn();
    })
    ["catch"](function (err) {
      // console.log('Failed to subscribe the user: ', err);
      updateBtn();
    });
}
