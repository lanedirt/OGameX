function filterToggle(event) {
  let filterTarget = event.target;
  let filterClass = getFilterClass(filterTarget.id);
  filterTarget = $(filterTarget);

  if (filterTarget.hasClass("filter_active")) {
    filterTarget.removeClass("filter_active");
    $(filterClass).each(function (i, obj) {
      $(this).removeClass("filtered_" + $(event.target)[0].id);
    });
    sendFilterToggle($(event.target)[0].id, 0);
    event.stopPropagation();
  } else {
    filterTarget.addClass("filter_active");
    $(filterClass).each(function (i, obj) {
      $(this).addClass("filtered_" + $(event.target)[0].id);
    });
    sendFilterToggle($(event.target)[0].id, 1);
    event.stopPropagation();
  }
}
