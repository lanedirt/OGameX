function deleteTemplate(id) {
  $.ajax({
    url: "/ajax/fleet/templates/" + id,
    type: "DELETE",
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    success: function (response) {
      if (response.success) {
        loadFleetTemplates();
        loadStandardFleetDropdown();
      } else {
        alert(response.message || "Failed to delete template.");
      }
    },
    error: function () {
      alert("Failed to delete template.");
    },
  });
}
