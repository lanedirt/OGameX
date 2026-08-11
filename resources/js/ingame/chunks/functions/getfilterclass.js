function getFilterClass(filterId) {
  let filterClass;

  switch (filterId) {
    case "filter_empty":
      filterClass = ".empty_filter";
      break;

    case "filter_inactive":
      filterClass = ".inactive_filter";
      break;

    case "filter_vacation":
      filterClass = ".vacation_filter";
      break;

    case "filter_strong":
      filterClass = ".strong_filter";
      break;

    case "filter_newbie":
      filterClass = ".newbie_filter";
      break;
  }

  return filterClass;
}
