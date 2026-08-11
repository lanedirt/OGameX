function initSubscriptionSystem() {
  pushButton = document.querySelector(".onoffswitch-checkbox");
  console.log(pushButton);

  if ("serviceWorker" in navigator && "PushManager" in window) {
    // console.log('Service Worker and Push is supported');
    pushButton.setAttribute("disabled", "disabled");
    pushButton.classList.add("disabled");
    console.log(pushButton);
    navigator.serviceWorker
      .register("sw.js")
      .then(function (swReg) {
        // console.log('Service Worker is registered', swReg);
        swRegistration = swReg;
        initializeUI();
      })
      ["catch"](function (error) {
        // console.error('Service Worker Error', error);
      });
  } else {
    // console.warn('Push messaging is not supported');
    pushButton.textContent = "Push Not Supported";
  }
}
