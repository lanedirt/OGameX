function openParentLocation(url) {
  try {
    document.location.href = url;
  } catch (error) {
    try {
      window.parent.document.location.href = url;
    } catch (error) {
      window.opener.document.location.href = url;
    }
  }
}
