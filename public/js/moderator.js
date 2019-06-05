$(document).ready(function() {
  $(window).on("resize", function(e) {
    checkScreenSize();
  });

  const MAX_WIDTH = 992;

  let isModalEnabled = false;
  let usersTableLines = document.querySelectorAll("#usersTable > tr");
  let reportsTableLines = document.querySelectorAll("#reportsTable > tr");

  checkScreenSize();

  function checkScreenSize() {
    let newWindowWidth = $(window).width();
    if (!isModalEnabled && newWindowWidth < MAX_WIDTH) {
      usersTableLines.forEach(function(line) {
        line.setAttribute("data-toggle", "modal");
        line.setAttribute("data-target", "#userActionsModal");
      });
      reportsTableLines.forEach(function(line) {
        line.setAttribute("data-toggle", "modal");
        line.setAttribute("data-target", "#reportActionsModal");
      });
      isModalEnabled = true;
    } else if (isModalEnabled && newWindowWidth >= MAX_WIDTH) {
      usersTableLines.forEach(function(line) {
        line.removeAttribute("data-toggle");
        line.removeAttribute("data-target");
      });
      reportsTableLines.forEach(function(line) {
        line.removeAttribute("data-toggle");
        line.removeAttribute("data-target");
      });
      isModalEnabled = false;
    }
  }

  $("#search-user").on("keyup", function() {
    var value = $(this)
      .val()
      .toLowerCase();
    $("#usersTable tr").filter(function() {
      $(this).toggle(
        $(this)
          .text()
          .toLowerCase()
          .indexOf(value) > -1
      );
    });
  });

  $("#search-report").on("keyup", function() {
    var value = $(this)
      .val()
      .toLowerCase();
    $("#reportsTable tr").filter(function() {
      $(this).toggle(
        $(this)
          .text()
          .toLowerCase()
          .indexOf(value) > -1
      );
    });
  });

  $("#search-review").on("keyup", function() {
    var value = $(this)
      .val()
      .toLowerCase();
    $("#reviewsTable tr").filter(function() {
      $(this).toggle(
        $(this)
          .text()
          .toLowerCase()
          .indexOf(value) > -1
      );
    });
  });
});
