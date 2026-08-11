function initFormValidation() {
  $("form.formValidation").validationEngine({
    validationEventTrigger: "keyup blur",
    promptPosition: "centerRight",
  });
}
