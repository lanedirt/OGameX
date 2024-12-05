function switchShopTab(obj, tab) {
    $('.tabSelectionTab').removeClass('active');
    obj.addClass('active');
    $.bbq.pushState({
      'page': tab
    });
  }