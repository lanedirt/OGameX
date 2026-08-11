function Marketplace(cfg) {
  this.tab = cfg.tab || "";
  this.loca = cfg.loca;
  this.constants = cfg.marketConstants;
  this.token = cfg.token;
  this.initMap = {
    buying: this.initTabBuying.bind(this),
    selling: this.initTabSelling.bind(this),
    overview: this.initTabOverview.bind(this),
    statistics: this.initTabStatistics.bind(this),
    history_buying: this.initTabHistoryBuying.bind(this),
    history_selling: this.initTabHistorySelling.bind(this),
    create_offer: this.initTabCreateOffer.bind(this),
  };

  if (this.initMap[this.tab]) {
    this.initMap[this.tab](cfg);
  }
}

Marketplace.prototype.onAjaxDone = function () {
  this.loadingIndicator.hide();
};

Marketplace.prototype.onAjaxError = function () {};

Marketplace.prototype.updateToken = function (token) {
  this.token = token;
}; //
// Item tabs general
//

Marketplace.prototype.initItemsCommon = function (cfg) {
  this.itemsWrapper = $(".marketplace .items_wrapper"); //this.itemsWrapper.mCustomScrollbar({theme: 'ogame'})

  this.loadingIndicator = this.itemsWrapper.ogameLoadingIndicator();
  this.items = $(".marketplace .items");
  this.table = this.items.closest(".og-table");
};

Marketplace.prototype.refreshItems = function (htmlItems) {
  this.items.html(htmlItems);
  this.table.toggleClass("isScrollbarVisible", this.items.children().length > 5);
};

Marketplace.prototype.getSorting = function () {
  let sorting = {
    playerName: this.sortPlayerName.getOrder(),
    price: this.sortPrice.getOrder(),
    deliveryTime: this.sortDelivery.getOrder(),
  };
  return sorting;
};

Marketplace.prototype.getFilters = function () {
  let filters = {};
  filters.resourceId = $("#filterResources").val();
  filters.shipId = $("#filterShips").val();
  filters.tradableItemId = $("#filterItems").val();
  filters.priceType = $("#filterPriceType").val();
  return filters;
};

Marketplace.prototype.getPagination = function () {
  let pagination = {};
  pagination.page = this.pagination.getPage();
  return pagination;
}; //
// Tab: buying
//

Marketplace.prototype.initTabBuying = function (cfg) {
  this.initItemsCommon(cfg);
  this.urlFetchBuyingItems = cfg.urlFetchBuyingItems || null;
  this.urlAccept = cfg.urlAccept;
  this.sortPlayerName = $("#sortPlayerName").ogameSortable({
    allowUnsorted: true,
    order: "",
    _token: this.token,
    onChange: this.onSortChangeBuying.bind(this),
  });
  this.sortDelivery = $("#sortDelivery").ogameSortable({
    allowUnsorted: true,
    order: "",
    align: "center",
    _token: this.token,
    onChange: this.onSortChangeBuying.bind(this),
  });
  this.sortPrice = $("#sortPrice").ogameSortable({
    allowUnsorted: true,
    order: "",
    align: "center",
    _token: this.token,
    onChange: this.onSortChangeBuying.bind(this),
  });
  this.pagination = $(".marketplace .pagination_wrapper").ogamePaginatable({
    page: 1,
    numPages: 1,
    _token: this.token,
    onChange: this.onPaginationChangeBuying.bind(this),
  });
  $(
    "#showFilterResources, #showFilterShips, #showFilterItems, #filterResources, #filterShips, #filterItems, #filterPriceType",
  ).on("change", this.onFilterChangeBuying.bind(this));
  this.items.on("click", ".item a.og-button.submit", this.onClickBuyingItem.bind(this));
  this.fetchBuyingItems();
};

Marketplace.prototype.onClickPriceMin = function (e) {
  e.preventDefault();
  let price = $(e.currentTarget).data("priceMin");
  this.inputPrice.val(price);

  if (this.quantity > 0) {
    this.setPrice(parseFloat(price));
  } else {
    this.setPrice(0);
  }
};

Marketplace.prototype.onClickPriceMax = function (e) {
  e.preventDefault();
  let price = $(e.currentTarget).data("priceMax");
  this.inputPrice.val(price);

  if (this.quantity > 0) {
    this.setPrice(parseFloat(price));
  } else {
    this.setPrice(0);
  }
};

Marketplace.prototype.onClickBuyingItem = function (e) {
  e.preventDefault();
  let marketItemId = $(e.currentTarget).data("itemid");
  let itemToken = $(e.currentTarget).data("_token");
  this.submitAcceptBuying(marketItemId, itemToken);
};

Marketplace.prototype.onSortChangeBuying = function (e) {
  this.token = e.token;
  this.fetchBuyingItems();
};

Marketplace.prototype.onFilterChangeBuying = function (e) {
  this.token = e.token;
  this.fetchBuyingItems();
};

Marketplace.prototype.onPaginationChangeBuying = function (e) {
  this.token = e.token;
  this.fetchBuyingItems();
};

Marketplace.prototype.onPaginationChangeHistoryBuying = function (e) {
  this.token = e.token;
  this.fetchHistoryBuyingItems();
};

Marketplace.prototype.onPaginationChangeHistorySelling = function (e) {
  this.token = e.token;
  this.fetchHistorySellingItems();
};

Marketplace.prototype.fetchBuyingItems = function () {
  this.loadingIndicator.show();
  let data = {
    sorting: this.getSorting(),
    filters: this.getFilters(),
    pagination: this.getPagination(),
    _token: this.token,
  };
  $.getJSON(this.urlFetchBuyingItems, data, this.onFetchBuyingItems.bind(this)).done(this.onAjaxDone.bind(this));
};

Marketplace.prototype.onFetchBuyingItems = function (data) {
  let htmlItems = data.content[data.target];
  this.updateToken(data.newToken);
  this.refreshItems(htmlItems);
};

Marketplace.prototype.submitAcceptBuying = function (marketItemId, itemToken) {
  let params = {
    marketItemId: marketItemId,
    _token: itemToken,
  };
  this.loadingIndicator.show();
  $.post(this.urlAccept, params, this.handleSubmitAcceptBuyingResponse.bind(this)).done(this.onAjaxDone.bind(this));
};

Marketplace.prototype.handleSubmitAcceptBuyingResponse = function (response) {
  let data = JSON.parse(response);
  let status = data.status || "failure";
  this.updateToken(data.newToken);

  if (status === "success") {
    this.fetchBuyingItems();
    fadeBox(data.message, false);
    getAjaxEventbox();
    getAjaxResourcebox();
  } else {
    this.displayErrors(data.errors);
  }
}; //
// Tab: selling
//

Marketplace.prototype.initTabSelling = function (cfg) {
  this.initItemsCommon(cfg);
  this.urlFetchSellingItems = cfg.urlFetchSellingItems || null;
  this.urlAccept = cfg.urlAccept;
  this.sortPlayerName = $("#sortPlayerName").ogameSortable({
    allowUnsorted: true,
    order: "",
    _token: this.token,
    onChange: this.onSortChangeSelling.bind(this),
  });
  this.sortDelivery = $("#sortDelivery").ogameSortable({
    allowUnsorted: true,
    order: "",
    align: "center",
    _token: this.token,
    onChange: this.onSortChangeSelling.bind(this),
  });
  this.sortPrice = $("#sortPrice").ogameSortable({
    allowUnsorted: true,
    order: "",
    align: "center",
    _token: this.token,
    onChange: this.onSortChangeSelling.bind(this),
  });
  this.pagination = $(".marketplace .pagination_wrapper").ogamePaginatable({
    page: 1,
    numPages: 1,
    _token: this.token,
    onChange: this.onPaginationChangeSelling.bind(this),
  });
  $(
    "#showFilterResources, #showFilterShips, #showFilterItems, #filterResources, #filterShips, #filterItems, #filterPriceType",
  ).on("change", this.onFilterChangeSelling.bind(this));
  this.items.on("click", ".item .og-button.submit", this.onClickSellingItem.bind(this));
  this.fetchSellingItems();
};

Marketplace.prototype.onClickSellingItem = function (e) {
  e.preventDefault();
  let marketItemId = $(e.currentTarget).data("itemid");
  let itemToken = $(e.currentTarget).data("_token");
  this.submitAcceptSelling(marketItemId, itemToken);
};

Marketplace.prototype.onSortChangeSelling = function (e) {
  this.token = e.token;
  this.fetchSellingItems();
};

Marketplace.prototype.onFilterChangeSelling = function (e) {
  this.token = e.token;
  this.fetchSellingItems();
};

Marketplace.prototype.onPaginationChangeSelling = function (e) {
  this.token = e.token;
  this.fetchSellingItems();
};

Marketplace.prototype.fetchSellingItems = function () {
  this.loadingIndicator.show();
  let data = {
    sorting: this.getSorting(),
    filters: this.getFilters(),
    pagination: this.getPagination(),
    _token: this.token,
  };
  $.getJSON(this.urlFetchSellingItems, data, this.onFetchSellingItems.bind(this)).done(this.onAjaxDone.bind(this));
};

Marketplace.prototype.onFetchSellingItems = function (data) {
  let htmlItems = data.content[data.target];
  this.updateToken(data.newToken);
  this.refreshItems(htmlItems);
};

Marketplace.prototype.submitAcceptSelling = function (marketItemId, itemToken) {
  let params = {
    marketItemId: marketItemId,
    _token: itemToken,
  };
  this.loadingIndicator.show();
  $.post(this.urlAccept, params, this.handleSubmitAcceptSellingResponse.bind(this)).done(this.onAjaxDone.bind(this));
};

Marketplace.prototype.handleSubmitAcceptSellingResponse = function (response) {
  let data = JSON.parse(response);
  let status = data.status || "failure";
  this.updateToken(data.newToken);

  if (status === "success") {
    this.fetchSellingItems();
    fadeBox(data.message, false);
    getAjaxEventbox();
    getAjaxResourcebox();
  } else {
    this.displayErrors(data.errors);
  }
}; //
// Tab: overview
//

Marketplace.prototype.initTabOverview = function (cfg) {
  this.initItemsCommon();
  this.urlFetchOverviewItems = cfg.urlFetchOverviewItems || null;
  this.items.on("click", ".og-button.delete", this.onClickDeleteItem.bind(this));
  this.sortType = $("#sortType").ogameSortable({
    allowUnsorted: true,
    order: "",
    align: "center",
    _token: this.token,
    onChange: this.onSortChangeOverview.bind(this),
  });
  this.sortPrice = $("#sortPrice").ogameSortable({
    allowUnsorted: true,
    order: "",
    align: "center",
    _token: this.token,
    onChange: this.onSortChangeOverview.bind(this),
  });
  this.pagination = $(".marketplace .pagination_wrapper").ogamePaginatable({
    page: 1,
    numPages: 1,
    _token: this.token,
    onChange: this.onPaginationChangeOverview.bind(this),
  });
  this.fetchOverviewItems();
};

Marketplace.prototype.getOverviewSorting = function () {
  let sorting = {
    type: this.sortType.getOrder(),
    price: this.sortPrice.getOrder(),
  };
  return sorting;
};

Marketplace.prototype.onSortChangeOverview = function (e) {
  this.token = e.token;
  this.fetchOverviewItems();
};

Marketplace.prototype.onPaginationChangeOverview = function (e) {
  this.token = e.token;
  this.fetchOverviewItems();
};

Marketplace.prototype.onClickDeleteItem = function (e) {
  e.stopPropagation();
  e.preventDefault();
  this.loadingIndicator.show();
  let urlDelete = e.currentTarget.href;
  let params = {
    _token: this.token,
  };
  let that = this;
  errorBoxDecision(
    this.loca.LOCA_ALL_NETWORK_ATTENTION,
    this.loca.LOCA_MARKET_CANCELLATION_NOTE,
    this.loca.LOCA_ALL_YES,
    this.loca.LOCA_ALL_NO,
    function () {
      $.post(urlDelete, params, that.handleSubmitDeleteResponse.bind(that)).done(that.onAjaxDone.bind(that));
    },
    function () {
      that.loadingIndicator.hide();
    },
  );
};

Marketplace.prototype.handleSubmitDeleteResponse = function (response) {
  let data = JSON.parse(response);
  let status = data.status || "failure";
  this.updateToken(data.newToken);

  if (status === "success") {
    this.fetchOverviewItems();
    getAjaxEventbox();
    getAjaxResourcebox();
  } else {
    this.displayErrors(data.errors);
  }
};

Marketplace.prototype.onItemDeleted = function () {
  this.onAjaxDone();
};

Marketplace.prototype.fetchOverviewItems = function () {
  this.loadingIndicator.show();
  let data = {
    sorting: this.getOverviewSorting(),
    pagination: this.getPagination(),
    _token: this.token,
  };
  $.getJSON(this.urlFetchOverviewItems, data, this.onFetchOverviewItems.bind(this)).done(this.onAjaxDone.bind(this));
};

Marketplace.prototype.onFetchOverviewItems = function (data) {
  let htmlItems = data.content[data.target];
  this.updateToken(data.newToken);
  this.refreshItems(htmlItems);
}; //
// Tab: statistics
//

Marketplace.prototype.initTabStatistics = function (cfg) {
  let statisticsChartData = cfg.statisticsChartData || null;
  this.lineChart = $("#chart_container").ogameLineChart(statisticsChartData);
  $("#showRatioMetal").on("change", this.refreshLineChart.bind(this));
  $("#showRatioCrystal").on("change", this.refreshLineChart.bind(this));
  $("#showRatioDeuterium").on("change", this.refreshLineChart.bind(this));
  this.refreshLineChart();
};

Marketplace.prototype.refreshLineChart = function () {
  this.lineChart.setDataSetVisible("metal", $("#showRatioMetal").is(":checked"));
  this.lineChart.setDataSetVisible("crystal", $("#showRatioCrystal").is(":checked"));
  this.lineChart.setDataSetVisible("deuterium", $("#showRatioDeuterium").is(":checked"));
  this.lineChart.render();
}; //
// Tab: history_buying
//

Marketplace.prototype.initTabHistoryBuying = function (cfg) {
  this.initItemsCommon();
  this.urlCollectItem = cfg.urlCollectItem;
  this.urlCollectPrice = cfg.urlCollectPrice;
  this.urlFetchHistoryBuyingItems = cfg.urlFetchHistoryBuyingItems || null;
  this.sortPayment = $("#sortPayment").ogameSortable({
    allowUnsorted: true,
    order: "",
    align: "center",
    _token: this.token,
    onChange: this.onSortChangeHistoryBuying.bind(this),
  });
  this.sortDate = $("#sortDate").ogameSortable({
    allowUnsorted: true,
    order: "desc",
    align: "center",
    _token: this.token,
    onChange: this.onSortChangeHistoryBuying.bind(this),
  });
  this.pagination = $(".marketplace .pagination_wrapper").ogamePaginatable({
    page: 1,
    numPages: 1,
    _token: this.token,
    onChange: this.onPaginationChangeHistoryBuying.bind(this),
  });
  this.items.on("click", ".item a.og-button.submit.collect-item", this.onClickCollectItem.bind(this));
  this.items.on("click", ".item a.og-button.submit.collect-price", this.onClickCollectPrice.bind(this));
  this.fetchHistoryBuyingItems();
};

Marketplace.prototype.onClickCollectItem = function (e) {
  e.preventDefault();
  let marketTransactionId = $(e.currentTarget).data("transactionid");
  let itemToken = $(e.currentTarget).data("_token");
  this.submitCollectItem(marketTransactionId, itemToken);
};

Marketplace.prototype.submitCollectItem = function (marketTransactionId, itemToken) {
  let params = {
    marketTransactionId: marketTransactionId,
    _token: itemToken,
  };
  this.loadingIndicator.show();
  $.post(this.urlCollectItem, params, this.handleSubmitCollectItemResponse.bind(this)).done(this.onAjaxDone.bind(this));
};

Marketplace.prototype.handleSubmitCollectItemResponse = function (response) {
  let data = JSON.parse(response);
  let status = data.status || "failure";
  let statusMessage = data.statusMessage || "";
  let marketTransactionId = data.marketTransactionId || 0;
  this.updateToken(data.newToken);

  if (status === "success") {
    $('.row.item[data-transactionid="' + marketTransactionId + '"] .col.date').show();
    $("a[data-token]").each(function () {
      $(this).data("_token", data.newToken);
      $(this).attr("data-token", data.newToken);
    });
    $('.collect-item[data-transactionid="' + marketTransactionId + '"]')
      .parent()
      .hide();
    fadeBox(data.message, false);
    getAjaxResourcebox();
  } else {
    this.displayErrors(data.errors);
  }
};

Marketplace.prototype.onClickCollectPrice = function (e) {
  e.preventDefault();
  let marketTransactionId = $(e.currentTarget).data("transactionid");
  let itemToken = $(e.currentTarget).data("_token");
  this.submitCollectPrice(marketTransactionId, itemToken);
};

Marketplace.prototype.submitCollectPrice = function (marketTransactionId, itemToken) {
  let params = {
    marketTransactionId: marketTransactionId,
    _token: itemToken,
  };
  this.loadingIndicator.show();
  $.post(this.urlCollectPrice, params, this.handleSubmitCollectPriceResponse.bind(this)).done(
    this.onAjaxDone.bind(this),
  );
};

Marketplace.prototype.handleSubmitCollectPriceResponse = function (response) {
  let data = JSON.parse(response);
  let status = data.status || "failure";
  let statusMessage = data.statusMessage || "";
  let marketTransactionId = data.marketTransactionId || 0;
  this.updateToken(data.newToken);

  if (status === "success") {
    $('.row.item[data-transactionid="' + marketTransactionId + '"] .col.date').show();
    $("a[data-token]").each(function () {
      $(this).data("_token", data.newToken);
      $(this).attr("data-token", data.newToken);
    });
    $('.collect-price[data-transactionid="' + marketTransactionId + '"]')
      .parent()
      .hide();
    fadeBox(data.message, false);
    getAjaxResourcebox();
  } else {
    this.displayErrors(data.errors);
  }
};

Marketplace.prototype.getHistorySorting = function () {
  return {
    price: this.sortPayment.getOrder(),
    date: this.sortDate.getOrder(),
  };
};

Marketplace.prototype.fetchHistoryBuyingItems = function () {
  this.loadingIndicator.show();
  let data = {
    sorting: this.getHistorySorting(),
    pagination: this.getPagination(),
    _token: this.token,
  };
  $.getJSON(this.urlFetchHistoryBuyingItems, data, this.onFetchHistoryBuyingItems.bind(this)).done(
    this.onAjaxDone.bind(this),
  );
};

Marketplace.prototype.onFetchHistoryBuyingItems = function (data) {
  let htmlItems = data.content[data.target];
  this.updateToken(data.newToken);
  this.refreshItems(htmlItems);
};

Marketplace.prototype.onSortChangeHistoryBuying = function (e) {
  this.token = e.token;
  this.fetchHistoryBuyingItems();
}; //
// Tab: history_selling
//

Marketplace.prototype.initTabHistorySelling = function (cfg) {
  this.initItemsCommon();
  this.urlCollectItem = cfg.urlCollectItem;
  this.urlCollectPrice = cfg.urlCollectPrice;
  this.sortPayment = $("#sortPayment").ogameSortable({
    allowUnsorted: true,
    order: "",
    align: "center",
    _token: this.token,
    onChange: this.onSortChangeHistorySelling.bind(this),
  });
  this.sortDate = $("#sortDate").ogameSortable({
    allowUnsorted: true,
    order: "desc",
    align: "center",
    _token: this.token,
    onChange: this.onSortChangeHistorySelling.bind(this),
  });
  this.pagination = $(".marketplace .pagination_wrapper").ogamePaginatable({
    page: 1,
    numPages: 1,
    _token: this.token,
    onChange: this.onPaginationChangeHistorySelling.bind(this),
  });
  this.urlFetchHistorySellingItems = cfg.urlFetchHistorySellingItems || null;
  this.items.on("click", ".item a.og-button.submit.collect-item", this.onClickCollectItem.bind(this));
  this.items.on("click", ".item a.og-button.submit.collect-price", this.onClickCollectPrice.bind(this));
  this.fetchHistorySellingItems();
};

Marketplace.prototype.fetchHistorySellingItems = function () {
  this.loadingIndicator.show();
  let data = {
    sorting: this.getHistorySorting(),
    pagination: this.getPagination(),
    _token: this.token,
  };
  $.getJSON(this.urlFetchHistorySellingItems, data, this.onFetchHistorySellingItems.bind(this)).done(
    this.onAjaxDone.bind(this),
  );
};

Marketplace.prototype.onFetchHistorySellingItems = function (data) {
  let htmlItems = data.content[data.target];
  this.updateToken(data.newToken);
  this.refreshItems(htmlItems);
};

Marketplace.prototype.onSortChangeHistorySelling = function (e) {
  this.token = e.token;
  this.fetchHistorySellingItems();
}; //
// Tab: create_offer
//

Marketplace.prototype.initTabCreateOffer = function (cfg) {
  this.content = $(".marketplace .content");
  this.loadingIndicator = this.content.ogameLoadingIndicator();
  this.ITEM_TYPE_SHIP = cfg.itemTypes.ITEM_TYPE_SHIP;
  this.ITEM_TYPE_RESOURCE = cfg.itemTypes.ITEM_TYPE_RESOURCE;
  this.ITEM_TYPE_TRADABLE_ITEM = cfg.itemTypes.ITEM_TYPE_TRADABLE_ITEM;
  this.MARKET_ITEM_TYPE_SELL_ORDER = cfg.marketItemTypes.SELL_ORDER;
  this.MARKET_ITEM_TYPE_BUY_ORDER = cfg.marketItemTypes.BUY_ORDER;
  this.RESOURCE_TYPE_METAL = cfg.resourceTypes.RESOURCE_TYPE_METAL;
  this.RESOURCE_TYPE_CRYSTAL = cfg.resourceTypes.RESOURCE_TYPE_CRYSTAL;
  this.RESOURCE_TYPE_DEUTERIUM = cfg.resourceTypes.RESOURCE_TYPE_DEUTERIUM;
  this.urlSubmitOffer = cfg.urlSubmitOffer;
  this.marketItemTypes = cfg.marketItemTypes;
  this.formSteps = cfg.formSteps;
  this.itemTypes = cfg.itemTypes;
  this.itemOptions = cfg.itemOptions;
  this.priceTypeOptions = cfg.priceTypeOptions;
  this.currentRatio = cfg.priceValidation.currentRatio;
  this.priceRangeLower = cfg.priceValidation.priceRangeLower;
  this.priceRangeUpper = cfg.priceValidation.priceRangeUpper;
  this.defaultPriceRange = cfg.defaultPriceRange;
  this.marketFee = cfg.marketFee;
  this.itemBox = $(".marketplace #itemBox");
  this.inputMarketItemType = $('.marketplace input[name="type"]');
  this.inputItemType = $('.marketplace input[name="itemType"]');
  this.inputQuantity = $('.marketplace input[name="quantity"]');
  this.inputPrice = $('.marketplace input[name="price"]');
  this.priceLimit = $(".marketplace #priceLimit");
  this.priceLimitMin = $(".marketplace #priceLimit #priceMin");
  this.priceLimitMax = $(".marketplace #priceLimit #priceMax");
  this.priceInformation = $("#orderPriceInformation");
  this.priceToPay = $("#priceToPay");
  this.priceInTotal = $("#priceInTotal");
  this.priceRangeValue = $("#priceRangeValue");
  this.marketFeeElem = $(".marketplace #marketFee");
  this.marketFeeColElem = $(".marketplace #colMarketFee");
  this.dropDownItemId = $(".marketplace #itemId");
  this.dropDownPriceType = $(".marketplace #priceType");
  this.dropDownPriceRange = $(".marketplace #priceRange");
  this.btnSubmitOffer = $("#submitOffer");
  this.dropDownPriceType.ogameDropDown();
  this.dropDownPriceRange.ogameDropDown();
  this.btnOptSellRequest = $("#btnOptSellRequest");
  this.btnOptBuyRequest = $("#btnOptBuyRequest");
  this.btnOptSellOrder = $("#btnOptSellOrder");
  this.btnOptBuyOrder = $("#btnOptBuyOrder");
  this.btnOptShips = $("#btnOptShips");
  this.btnOptResources = $("#btnOptResources");
  this.btnOptItems = $("#btnOptItems");
  this.btnSubmitOffer.on("click", this.onClickSubmitOffer.bind(this));
  this.inputMarketItemType.on("change", this.onChangeMarketItemType.bind(this));
  this.inputItemType.on("change", this.onChangeItemType.bind(this));
  this.inputQuantity.on("blur", this.onChangeQuantity.bind(this));
  this.inputQuantity.on("focus", this.onFocusQuantity.bind(this));
  this.inputQuantity.on("keyup", this.onKeyInputQuantity.bind(this));
  this.inputPrice.on("blur", this.onChangePrice.bind(this));
  this.inputPrice.on("focus", this.onFocusPrice.bind(this));
  this.inputPrice.on("keyup", this.onKeyInputPrice.bind(this));
  this.dropDownItemId.on("change", this.onChangeItemId.bind(this));
  this.dropDownPriceType.on("change", this.onChangePriceType.bind(this));
  this.dropDownPriceRange.on("change", this.onChangePriceRange.bind(this));
  this.priceLimitMin.on("click", this.onClickPriceMin.bind(this));
  this.priceLimitMax.on("click", this.onClickPriceMax.bind(this));
  this.resetPriceType();
  this.resetMarketItemType();
  this.resetItemType();
  this.refreshAvailableItemTypes();
  this.resetPrice();
  this.resetPriceRange();
  this.refreshMarketItemTypes();
  this.refreshFormSteps();
  this.refreshItemTypes();
  this.refreshItemIdOptions();
  this.refreshItemId();
  this.refreshItemBox();
  this.refreshPriceRangeVisiblity();
  this.refreshPrice();
};

Marketplace.prototype.onClickSubmitOffer = function (e) {
  e.preventDefault();
  this.submitOffer();
};

Marketplace.prototype.refreshAvailableItemTypes = function () {
  let minQuantity = this.getMinQuantityByItemType(this.ITEM_TYPE_SHIP);
  let itemOptions = this.getItemOptionsByItemType(this.ITEM_TYPE_SHIP, minQuantity);

  if (this.MARKET_ITEM_TYPE_SELL_ORDER === this.marketItemType) {
    this.marketFeeColElem.show();

    if (itemOptions.length == 0) {
      this.btnOptShips.attr("disabled", "disabled");
      $("label[for='btnOptShips']").addClass("disabled");
    }
  } else if (this.MARKET_ITEM_TYPE_BUY_ORDER === this.marketItemType) {
    this.marketFeeColElem.hide();
    this.btnOptShips.removeAttr("disabled");
    $("label[for='btnOptShips']").removeClass("disabled");
  }

  minQuantity = this.getMinQuantityByItemType(this.ITEM_TYPE_TRADABLE_ITEM);
  itemOptions = this.getItemOptionsByItemType(this.ITEM_TYPE_TRADABLE_ITEM, minQuantity);

  if (this.MARKET_ITEM_TYPE_SELL_ORDER === this.marketItemType) {
    this.marketFeeColElem.show();

    if (itemOptions.length == 0) {
      this.btnOptItems.attr("disabled", "disabled");
      $("label[for='btnOptItems']").addClass("disabled");
    }
  } else if (this.MARKET_ITEM_TYPE_BUY_ORDER === this.marketItemType) {
    this.marketFeeColElem.hide();
    this.btnOptItems.removeAttr("disabled");
    $("label[for='btnOptItems']").removeClass("disabled");
  }
};

Marketplace.prototype.submitOffer = function () {
  let params = {
    marketItemType: this.marketItemType,
    itemType: this.itemType,
    itemId: this.itemId,
    quantity: this.quantity,
    priceType: this.priceType,
    price: this.price,
    _token: this.token,
  };

  if (this.isOrder()) {
    params.priceRange = this.priceRange;
  }

  this.loadingIndicator.show();
  $.post(this.urlSubmitOffer, params, this.handleSubmitOfferResponse.bind(this)).done(this.onAjaxDone.bind(this));
};

Marketplace.prototype.handleSubmitOfferResponse = function (response) {
  let data = JSON.parse(response);
  let status = data.status || "failure";
  this.updateToken(data.newToken);

  if (status === "success") {
    window.location = data.redirectUrl;
  } else {
    this.displayErrors(data.errors);
  }
};

Marketplace.prototype.resetMarketItemType = function () {
  this.setMarketItemType(this.getFirstMarketItemType());
};

Marketplace.prototype.setMarketItemType = function (marketItemType) {
  let marketItemTypeOld = this.marketItemType;
  this.marketItemType = parseInt(marketItemType);

  if (this.marketItemType !== marketItemTypeOld) {
    if (this.marketItemType === this.MARKET_ITEM_TYPE_BUY_ORDER) {
      this.priceInformation.show();
    }

    if (this.marketItemType === this.MARKET_ITEM_TYPE_SELL_ORDER) {
      this.priceInformation.hide();
    }

    this.resetItemType();
    this.resetItemId();
    this.refreshAvailableItemTypes();
    this.refreshItemTypes();
    this.refreshItemIdOptions();
    this.refreshItemId();
    this.refreshItemBox();
    this.refreshFormSteps();
    this.refreshPriceRangeVisiblity();
  }
};

Marketplace.prototype.resetItemType = function () {
  this.setItemType(this.getFirstItemType());
};

Marketplace.prototype.setItemType = function (itemType) {
  let itemTypeOld = this.itemType;
  this.itemType = parseInt(itemType);

  if (itemTypeOld !== this.itemType) {
    this.resetItemId();
    this.resetPriceTypeOnEqualResource();
    this.refreshItemTypes();
    this.refreshItemIdOptions();
    this.refreshItemId();
    this.refreshItemBox();
  }
};

Marketplace.prototype.resetItemId = function () {
  this.setItemId(this.getFirstItemId(this.itemType));
};

Marketplace.prototype.setItemId = function (itemId) {
  // tradable items use string ids
  if (this.itemType !== this.ITEM_TYPE_TRADABLE_ITEM) {
    itemId = parseInt(itemId);
  }

  let itemIdOld = this.itemId;
  this.itemId = itemId;

  if (itemIdOld !== this.itemId) {
    this.refreshItemBox();
    this.resetQuantity();
    this.resetPriceTypeOnEqualResource();
  }
};

Marketplace.prototype.resetPriceTypeOnEqualResource = function () {
  // can not trade e.g. metal for metal
  if (this.itemType === this.ITEM_TYPE_RESOURCE && this.itemId === this.priceType) {
    switch (this.itemId) {
      case this.RESOURCE_TYPE_METAL:
        this.setPriceType(this.RESOURCE_TYPE_CRYSTAL);
        break;

      case this.RESOURCE_TYPE_CRYSTAL:
        this.setPriceType(this.RESOURCE_TYPE_DEUTERIUM);
        break;

      case this.RESOURCE_TYPE_DEUTERIUM:
        this.setPriceType(this.RESOURCE_TYPE_METAL);
        break;
    }
  }
};

Marketplace.prototype.getDefaultQuantity = function () {
  let itemOption = this.getItemOption(this.itemType, this.itemId);
  return itemOption.minQuantity;
};

Marketplace.prototype.resetQuantity = function () {
  this.setQuantity(this.getDefaultQuantity(), true);
};

Marketplace.prototype.setQuantity = function (quantity, refresh) {
  refresh = refresh || false;
  let quantityOld = this.quantity;
  let itemOption = this.getItemOption(this.itemType, this.itemId);
  let quantityMax = itemOption !== undefined ? Math.min(itemOption.quantity, itemOption.maxQuantity) : 0;
  quantityMax = Math.min(quantityMax, this.constants.MARKET_MAX_QUANTITY);

  if (this.isSelling()) {
    this.quantity = clampInt(quantity, itemOption.minQuantity, quantityMax);
  }

  if (this.isBuying()) {
    this.quantity = clampInt(quantity, itemOption.minQuantity, this.constants.MARKET_MAX_QUANTITY);
  }

  if (this.quantity !== quantityOld || this.quantity !== parseInt(quantity) || refresh === true) {
    this.resetPrice();
    this.refreshQuantity();
    this.refreshPrice();
    this.refreshPriceLimit();
  }
};

Marketplace.prototype.resetPrice = function () {
  let price = this.getPriceCalculated(this.quantity);
  this.setPrice(price);
};

Marketplace.prototype.setPrice = function (price) {
  let priceOld = this.price;
  let priceCalculated = this.getPriceCalculated(this.quantity);
  let priceMin = this.getPriceMin(this.quantity);
  let priceMax = this.getPriceMax(this.quantity);

  if (priceCalculated === undefined || priceMin === undefined || priceMax === undefined || this.quantity === 0) {
    this.price = 0;
  } else {
    this.price = clampFloat(price, priceMin, priceMax);
  }

  if (this.price !== priceOld || this.price !== price) {
    this.refreshPrice();
    this.refreshPriceValidation();
    this.refreshPriceLimit();
  }
};

Marketplace.prototype.resetPriceType = function () {
  this.setPriceType(this.getFirstResourceType());
  this.refreshPriceType();
};

Marketplace.prototype.setPriceType = function (priceType) {
  let priceTypeOld = this.priceType;
  this.priceType = parseInt(priceType);

  if (this.priceType !== priceTypeOld) {
    this.resetPrice();
    this.refreshPriceType();
  }
};

Marketplace.prototype.resetPriceRange = function () {
  this.setPriceRange(this.defaultPriceRange);
};

Marketplace.prototype.setPriceRange = function (priceRange) {
  let priceRangeOld = this.priceRange;
  this.priceRange = parseInt(priceRange);

  if (this.priceRange !== priceRangeOld) {
    this.refreshPrice();
  }
};

Marketplace.prototype.onChangeMarketItemType = function (e) {
  this.setMarketItemType($('.marketplace input[name="type"]:checked').val());
};

Marketplace.prototype.onChangeItemType = function (e) {
  this.setItemType($('.marketplace input[name="itemType"]:checked').val());
};

Marketplace.prototype.onChangeItemId = function (e) {
  this.setItemId(this.dropDownItemId.val());
};

Marketplace.prototype.onChangeQuantity = function (e) {
  this.setQuantity(this.inputQuantity.val());
};

Marketplace.prototype.onFocusQuantity = function (e) {
  this.inputQuantity.val("");
};

Marketplace.prototype.onKeyInputQuantity = function (e) {
  if (e.which == 75) {
    let val = parseInt($(e.target).val()) || 0;

    if (val == 0) {
      val = 1;
    }

    val = val + "000";
    $(e.target).val(val);
  }
};

Marketplace.prototype.onChangePrice = function (e) {
  if (this.quantity > 0) {
    this.setPrice(parseFloat(this.inputPrice.val()));
  } else {
    this.setPrice(0);
  }
};

Marketplace.prototype.onFocusPrice = function (e) {
  this.inputPrice.val("");
};

Marketplace.prototype.onKeyInputPrice = function (e) {
  if (e.which == 75) {
    let val = parseInt($(e.target).val()) || 0;

    if (val == 0) {
      val = 1;
    }

    val = val + "000";
    $(e.target).val(val);
  }
};

Marketplace.prototype.onChangePriceType = function (e) {
  this.setPriceType(this.dropDownPriceType.val());
};

Marketplace.prototype.onChangePriceRange = function (e) {
  this.setPriceRange(this.dropDownPriceRange.val());
  this.refreshPriceInformation();
};

Marketplace.prototype.refreshMarketItemTypes = function () {
  this.btnOptSellOrder.prop("checked", this.marketItemType === this.MARKET_ITEM_TYPE_SELL_ORDER);
  this.btnOptBuyOrder.prop("checked", this.marketItemType === this.MARKET_ITEM_TYPE_BUY_ORDER);
};

Marketplace.prototype.refreshFormSteps = function () {
  let formSteps = this.formSteps[this.marketItemType] || [];
  $(".marketplace .og-sub-step.sub-step-1").html(formSteps[0] || "");
  $(".marketplace .og-sub-step.sub-step-2").html(formSteps[1] || "");
  $(".marketplace .og-sub-step.sub-step-3").html(formSteps[2] || "");
};

Marketplace.prototype.refreshItemTypes = function () {
  this.btnOptShips.prop("checked", this.itemType === this.ITEM_TYPE_SHIP);
  this.btnOptResources.prop("checked", this.itemType === this.ITEM_TYPE_RESOURCE);
  this.btnOptItems.prop("checked", this.itemType === this.ITEM_TYPE_TRADABLE_ITEM);
};

Marketplace.prototype.refreshItemBox = function () {
  let itemOption = this.getItemOption(this.itemType, this.itemId);

  if (itemOption) {
    let html = "";

    if (itemOption.type === this.ITEM_TYPE_TRADABLE_ITEM) {
      html = '<img src="' + itemOption.itemImage + '"/>';
    } else {
      html = '<div class="sprite ' + itemOption.cssClass + '"></div>';
    }

    this.itemBox.find(".thumbnail").html(html);
    this.itemBox.find(".quantity").html(tsdpkt(itemOption.quantity));
  } else {
    this.itemBox.find(".thumbnail").html("");
    this.itemBox.find(".quantity").html("-");
  }
};

Marketplace.prototype.refreshItemIdOptions = function () {
  this.dropDownItemId.ogameDropDown("destroy");
  this.dropDownItemId.html("");
  let htmlOptions = "";

  if (this.ITEM_TYPE_SHIP === this.itemType && this.MARKET_ITEM_TYPE_SELL_ORDER === this.marketItemType) {
    this.itemOptions.ships.forEach(function (option) {
      if (option.quantity > 0) {
        htmlOptions += '<option value="' + option.value + '">' + option.name + "</option>";
      }
    });
  }

  if (this.ITEM_TYPE_SHIP === this.itemType && this.MARKET_ITEM_TYPE_BUY_ORDER === this.marketItemType) {
    this.itemOptions.ships.forEach(function (option) {
      htmlOptions += '<option value="' + option.value + '">' + option.name + "</option>";
    });
  }

  if (this.ITEM_TYPE_RESOURCE === this.itemType) {
    this.itemOptions.resources.forEach(function (option) {
      htmlOptions += '<option value="' + option.value + '">' + option.name + "</option>";
    });
  }

  if (this.ITEM_TYPE_TRADABLE_ITEM === this.itemType && this.MARKET_ITEM_TYPE_SELL_ORDER === this.marketItemType) {
    this.itemOptions.items.forEach(function (option) {
      if (option.quantity > 0) {
        htmlOptions += '<option value="' + option.value + '">' + option.name + "</option>";
      }
    });
  }

  if (this.ITEM_TYPE_TRADABLE_ITEM === this.itemType && this.MARKET_ITEM_TYPE_BUY_ORDER === this.marketItemType) {
    this.itemOptions.items.forEach(function (option) {
      htmlOptions += '<option value="' + option.value + '">' + option.name + "</option>";
    });
  }

  this.dropDownItemId.html(htmlOptions).ogameDropDown();
};

Marketplace.prototype.refreshItemId = function () {
  this.dropDownItemId.find("option").removeAttr("selected");

  if (this.itemId) {
    this.dropDownItemId.find('option[value="' + this.itemId + '"]').attr("selected", "selected");
  }
};

Marketplace.prototype.refreshQuantity = function () {
  this.inputQuantity.val(this.quantity);
};

Marketplace.prototype.refreshPriceRangeVisiblity = function () {
  if (this.isOrder()) {
    $("#colPriceRange").show();
  } else {
    $("#colPriceRange").hide();
  }
};

Marketplace.prototype.refreshPrice = function () {
  this.refreshPriceRange();
  let priceTotal = parseInt(this.price);
  this.inputPrice.val(priceTotal);
  this.refreshPriceLimit();
  this.refreshMarketFee();
  this.refreshPriceInformation();
};

Marketplace.prototype.refreshPriceType = function () {
  this.dropDownPriceType.find("option").removeAttr("selected");

  if (this.priceType) {
    this.dropDownPriceType.val(this.priceType);
    this.dropDownPriceType.find('option[value="' + this.priceType + '"]').attr("selected", "selected");
    this.dropDownPriceType.ogameDropDown("refresh");
  }
};

Marketplace.prototype.refreshPriceRange = function () {
  this.dropDownPriceRange.find("option").removeAttr("selected");

  if (this.priceRange) {
    this.dropDownPriceRange.find('option[value="' + this.priceRange + '"]').attr("selected", "selected");
    this.dropDownPriceRange.val(this.priceRange);
    this.dropDownPriceRange.ogameDropDown("refresh");
  }
};

Marketplace.prototype.refreshPriceLimit = function () {
  let priceMin = this.getPriceMin(this.quantity);
  let priceMax = this.getPriceMax(this.quantity);

  if (priceMin === undefined || priceMax === undefined) {
    this.priceLimitMin.html("");
    this.priceLimitMax.html("");
    this.priceLimitMin.data("priceMin", 0);
    this.priceLimitMax.data("priceMax", 0);
  } else {
    this.priceLimitMin.html(tsdpkt(priceMin));
    this.priceLimitMin.data("priceMin", priceMin);
    this.priceLimitMax.html(tsdpkt(priceMax));
    this.priceLimitMax.data("priceMax", priceMax);
  }
};

Marketplace.prototype.refreshPriceValidation = function () {};

Marketplace.prototype.refreshMarketFee = function () {
  let priceTypeOption = this.getPriceTypeOption(this.priceType);

  if (this.isOrder()) {
    let priceMin = this.getPriceMin(this.quantity);
    let priceMax = this.getPriceMax(this.quantity);
    let feeMin = Math.floor(this.price * (1.0 - this.priceRange / 100) * this.marketFee);
    let feeMax = Math.floor(this.price * (1.0 + this.priceRange / 100) * this.marketFee);
    this.marketFeeElem.html(tsdpkt(feeMin) + " - " + tsdpkt(feeMax) + " " + priceTypeOption.name);
  } else {
    let fee = Math.floor(this.price * this.marketFee);
    this.marketFeeElem.html(tsdpkt(fee) + " " + priceTypeOption.name);
  }
};

Marketplace.prototype.refreshPriceInformation = function () {
  let priceToPayCalc = Math.floor(this.price * (1 + $("#priceRange option:selected").val() / 100));
  this.priceToPay.html(tsdpkt(priceToPayCalc));
  this.priceInTotal.html(tsdpkt(this.price));
  this.priceRangeValue.html($("#priceRange option:selected").text());
};

Marketplace.prototype.getFirstMarketItemType = function () {
  return parseInt(this.marketItemTypes[Object.keys(this.marketItemTypes)[0]]);
};

Marketplace.prototype.getFirstItemType = function () {
  let itemType = parseInt(this.itemTypes[Object.keys(this.itemTypes)[0]]);
  let minQuantity = this.getMinQuantityByItemType(itemType);
  let itemOptions = this.getItemOptionsByItemType(itemType, minQuantity);

  if (itemOptions.length == 0) {
    return this.ITEM_TYPE_RESOURCE;
  }

  return parseInt(this.itemTypes[Object.keys(this.itemTypes)[0]]);
};

Marketplace.prototype.getMinQuantityByItemType = function (itemType) {
  if (
    (this.ITEM_TYPE_TRADABLE_ITEM === itemType || this.ITEM_TYPE_SHIP === itemType) &&
    this.MARKET_ITEM_TYPE_SELL_ORDER === this.marketItemType
  ) {
    return 1;
  }

  return 0;
};

Marketplace.prototype.getItemOptionsByItemType = function (itemType, minQuantity) {
  let itemOptionsSelected = [];

  if (itemType === undefined) {
    return itemOptionsSelected;
  }

  let items;

  switch (itemType) {
    case this.ITEM_TYPE_SHIP:
      items = this.itemOptions.ships;
      break;

    case this.ITEM_TYPE_RESOURCE:
      items = this.itemOptions.resources;
      break;

    case this.ITEM_TYPE_TRADABLE_ITEM:
      items = this.itemOptions.items;
      break;
  }

  for (let i = 0; i < items.length; i++) {
    if (items[i].quantity >= minQuantity) {
      itemOptionsSelected.push(items[i]);
    }
  }

  return itemOptionsSelected;
};

Marketplace.prototype.getFirstItemId = function (itemType) {
  let minQuantity = this.getMinQuantityByItemType(itemType);
  let itemOptions = this.getItemOptionsByItemType(itemType, minQuantity);
  return itemOptions.length > 0 ? itemOptions[0].value : undefined;
};

Marketplace.prototype.getItemOption = function (itemType, itemId) {
  if (itemType === undefined) {
    return undefined;
  }

  let minQuantity = this.getMinQuantityByItemType(itemType);
  let itemOptions = this.getItemOptionsByItemType(itemType, minQuantity);
  return itemOptions.find(function (itemOption) {
    return itemOption.value === itemId;
  });
};

Marketplace.prototype.getPriceTypeOption = function (priceType) {
  return this.priceTypeOptions.find(function (priceTypeOption) {
    return priceTypeOption.value === priceType;
  });
};

Marketplace.prototype.getFirstResourceType = function () {
  return this.itemOptions.resources[0].value;
};

Marketplace.prototype.isOrder = function () {
  return (
    this.marketItemType === this.MARKET_ITEM_TYPE_BUY_ORDER || this.marketItemType === this.MARKET_ITEM_TYPE_SELL_ORDER
  );
};

Marketplace.prototype.isBuying = function () {
  return this.marketItemType === this.MARKET_ITEM_TYPE_BUY_ORDER;
};

Marketplace.prototype.isSelling = function () {
  return this.marketItemType === this.MARKET_ITEM_TYPE_SELL_ORDER;
};

Marketplace.prototype.displayErrors = function (errors) {
  // only display the first error
  let error = errors[0] || undefined;

  if (error) {
    fadeBox(error.message, true);
  }
};

Marketplace.prototype.isPriceValid = function () {
  let priceCalculated = this.getPriceCalculated(this.quantity);
  let priceMin = this.getPriceMin(this.quantity);
  let priceMax = this.getPriceMax(this.quantity);

  if (priceCalculated === undefined || priceMin === undefined || priceMax === undefined) {
    return false;
  }

  return priceMin < priceCalculated && priceCalculated < priceMax;
};

Marketplace.prototype.getPriceCalculated = function (quantity) {
  let itemOption = this.getItemOption(this.itemType, this.itemId);

  if (itemOption === undefined) {
    return undefined;
  }

  return Math.floor(this.convertMCDTo(itemOption.priceCalculatedInMCD * quantity, this.priceType));
};

Marketplace.prototype.getPriceMin = function (quantity) {
  let priceCalculated = this.getPriceCalculated(quantity);

  if (priceCalculated !== undefined) {
    return this.calcMinPrice(priceCalculated);
  }

  return undefined;
};

Marketplace.prototype.getPriceMax = function (quantity) {
  let priceCalculated = this.getPriceCalculated(quantity);

  if (priceCalculated !== undefined) {
    return this.calcMaxPrice(priceCalculated);
  }

  return undefined;
};

Marketplace.prototype.convertMCDTo = function (mcd, priceType) {
  switch (priceType) {
    case this.RESOURCE_TYPE_METAL:
      return this.convertMCDToMetal(mcd);

    case this.RESOURCE_TYPE_CRYSTAL:
      return this.convertMCDToCrystal(mcd);

    case this.RESOURCE_TYPE_DEUTERIUM:
      return this.convertMCDToDeuterium(mcd);
  }

  return 0;
};

Marketplace.prototype.convertMCDToMetal = function (mcd) {
  return mcd;
};

Marketplace.prototype.convertMCDToCrystal = function (mcd) {
  return mcd * (this.currentRatio.crystal / this.currentRatio.metal);
};

Marketplace.prototype.convertMCDToDeuterium = function (mcd) {
  return mcd * (this.currentRatio.deuterium / this.currentRatio.metal);
};

Marketplace.prototype.calcMinPrice = function (priceCalculated) {
  return Math.max(Math.floor(priceCalculated * (1.0 - this.priceRangeLower)), 1);
};

Marketplace.prototype.calcMaxPrice = function (priceCalculated) {
  return Math.floor(priceCalculated * (1.0 + this.priceRangeUpper));
};

Marketplace.prototype.getMetalAvailable = function () {
  return this.getResourceAvailable(this.RESOURCE_TYPE_CRYSTAL);
};

Marketplace.prototype.getCrystalAvailable = function () {
  return this.getResourceAvailable(this.RESOURCE_TYPE_CRYSTAL);
};

Marketplace.prototype.getDeuteriumAvailable = function () {
  return this.getResourceAvailable(this.RESOURCE_TYPE_CRYSTAL);
};

Marketplace.prototype.getResourceAvailable = function (resourceType) {
  let itemOption = this.getItemOption(this.ITEM_TYPE_RESOURCE, resourceType);
  return itemOption !== undefined ? itemOption.quantity : 0;
};
