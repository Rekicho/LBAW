$(document).ready(function () {
  $(window).on("resize", function (e) {
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
      usersTableLines.forEach(function (line) {
        line.setAttribute("data-toggle", "modal");
        line.setAttribute("data-target", "#userActionsModal");
      });
      reportsTableLines.forEach(function (line) {
        line.setAttribute("data-toggle", "modal");
        line.setAttribute("data-target", "#reportActionsModal");
      });
      isModalEnabled = true;
    } else if (isModalEnabled && newWindowWidth >= MAX_WIDTH) {
      usersTableLines.forEach(function (line) {
        line.removeAttribute("data-toggle");
        line.removeAttribute("data-target");
      });
      reportsTableLines.forEach(function (line) {
        line.removeAttribute("data-toggle");
        line.removeAttribute("data-target");
      });
      isModalEnabled = false;
    }
  }

  function getPaginationSelectedPage(url) {
    let chunks = url.split('?');
    let querystr = chunks[1].split('&');
    let pg = 1;
    for (i in querystr) {
      let qs = querystr[i].split('=');
      if (qs[0] == 'page') {
        pg = qs[1];
        break;
      }
    }
    return pg;
  }

  $('#users').on('click', '.pagination a', function (e) {
    e.preventDefault();
    let pg = getPaginationSelectedPage($(this).attr('href'));

    $.ajax({
      url: '/back-office/moderator/ajax/users',
      data: { page: pg },
      success: function (data) {
        $('#users').html(data);
      }
    });
  });

  $('#reports').on('click', '.pagination a', function (e) {
    e.preventDefault();
    let pg = getPaginationSelectedPage($(this).attr('href'));

    $.ajax({
      url: '/back-office/moderator/ajax/reports',
      data: { page: pg },
      success: function (data) {
        $('#reports').html(data);
      }
    });
  });

  $('#reviews').on('click', '.pagination a', function (e) {
    e.preventDefault();
    let pg = getPaginationSelectedPage($(this).attr('href'));

    $.ajax({
      url: '/back-office/moderator/ajax/reviews',
      data: { page: pg },
      success: function (data) {
        $('#reviews').html(data);
      }
    });
  });

  $('#users').load('/back-office/moderator/ajax/users?page=1');
  $('#reports').load('/back-office/moderator/ajax/reports?page=1');
  $('#reviews').load('/back-office/moderator/ajax/reviews?page=1');

  $("#search-user").on("keyup", function () {
    console.log(1)
    let value = $(this)
      .val()
      .toLowerCase();
    $("#usersTable tr").filter(function () {
      $(this).toggle(
        $(this)
          .text()
          .toLowerCase()
          .indexOf(value) > -1
      );
    });
  });

  $("#search-report").on("keyup", function () {
    let value = $(this)
      .val()
      .toLowerCase();
    $("#reportsTable tr").filter(function () {
      $(this).toggle(
        $(this)
          .text()
          .toLowerCase()
          .indexOf(value) > -1
      );
    });
  });

  $("#search-review").on("keyup", function () {
    let value = $(this)
      .val()
      .toLowerCase();
    $("#reviewsTable tr").filter(function () {
      $(this).toggle(
        $(this)
          .text()
          .toLowerCase()
          .indexOf(value) > -1
      );
    });
  });
});