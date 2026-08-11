function onClickExecutePtrCopy(e) {
  e.stopPropagation();
  e.preventDefault();
  errorBoxDecision(
    LocalizationStrings.question,
    preferenceLoca.copyToPtrQuestion,
    LocalizationStrings.yes,
    LocalizationStrings.no,
    function () {
      $.post(
        $(".copy2PtrConainer a").attr("href"),
        {
          _token: token,
        },
        (response) => {
          let data = JSON.parse(response);
          token = data.newAjaxToken;

          if (data.status === "success") {
            console.log(data.content);
            $(".copy2PtrConainer .fieldwrapper").replaceWith(data.content);
          }
        },
      );
    },
  );
}
