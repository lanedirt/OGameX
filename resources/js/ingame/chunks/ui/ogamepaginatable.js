function OGamePaginatable(container, data) {
  this.container = container;
  this.page = data.page || 0;
  this.numPages = data.numPages || 0;
  this.size = data.size || 10;
  this.token = data.token || null;
  this.onChange = data.onChange || null;
}

OGamePaginatable.prototype.init = function () {
  let label = this.container.text();
  let html = '<ul class="og-paginatable"></ul>';
  this.element = $(html);
  this.container.html(this.element);
  $(this.element).on("click", "li", this.handleClick.bind(this));
  this.refresh();
};

OGamePaginatable.prototype.handleClick = function (e) {
  let page = $(e.currentTarget).data("page");

  switch (page) {
    case "page-first":
      this.page = 1;
      break;

    case "page-left":
      this.page = clampInt(parseInt(this.page - 1), 1, this.numPages);
      break;

    case "page-last":
      this.page = this.numPages;
      break;

    case "page-right":
      this.page = clampInt(parseInt(this.page + 1), 1, this.numPages);
      break;

    default:
      this.page = clampInt(parseInt(page), 1, this.numPages);
      break;
  }

  this.refresh();
  this.notifyChange();
};

OGamePaginatable.prototype.update = function (data) {
  this.page = data.page || this.page;
  this.numPages = data.numPages || this.numPages;
  this.token = data.token || this.token;
  this.refresh();
};

OGamePaginatable.prototype.getPage = function () {
  return this.page;
};

OGamePaginatable.prototype.getNumPages = function () {
  return this.numPages;
};

OGamePaginatable.prototype.notifyChange = function () {
  if (this.onChange) {
    this.onChange({
      page: this.page,
      numPages: this.numPages,
      _token: this.token,
    });
  }
};

OGamePaginatable.prototype.refresh = function () {
  if (this.numPages === 0) {
    this.element.html("-");
    return;
  }

  let pageStart = this.page - Math.floor(this.size / 2);
  let pageEnd = pageStart + this.size;

  if (pageStart < 1) {
    let delta = 1 - pageStart;
    pageStart += delta;
    pageEnd += delta;
  } else if (pageEnd > this.numPages) {
    let delta = this.numPages - pageEnd;
    pageStart += delta;
    pageEnd += delta;
  }

  pageStart = clampInt(pageStart, 1, this.numPages);
  pageEnd = clampInt(pageEnd, 1, this.numPages);
  let pages = [];
  pages.push('<li data-page="page-first">&lt;&lt;</li>');
  pages.push('<li data-page="page-left">&lt;</li>');

  for (let page = pageStart; page <= pageEnd; ++page) {
    let stateClass = page === this.page ? "active" : "";
    pages.push('<li data-page="' + page + '" class="' + stateClass + '">' + page + "</li>");
  }

  pages.push('<li data-page="page-right">&gt;</li>');
  pages.push('<li data-page="page-last">&gt;&gt;</li>');
  this.element.html(pages.join(""));
};
