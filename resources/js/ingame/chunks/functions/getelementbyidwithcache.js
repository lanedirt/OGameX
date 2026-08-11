function getElementByIdWithCache(uid) {
  if (!DOM_GET_ELEMENT_BY_ID_CACHE[uid]) {
    DOM_GET_ELEMENT_BY_ID_CACHE[uid] = document.getElementById(uid);
  }

  return DOM_GET_ELEMENT_BY_ID_CACHE[uid];
}
