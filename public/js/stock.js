$(document).ready(function () {

  $(window).on("resize", function (e) {
    checkScreenSize();
  });

  const MAX_WIDTH = 992;

  let isModalEnabled = false;
  let productsTableLines = document.querySelectorAll("#productsTable > tr");

  checkScreenSize();

  $("#search-product").on("keyup", function () {
    var value = $(this)
      .val()
      .toLowerCase();
    $("#productsTable tr").filter(function () {
      $(this).toggle(
        $(this)
          .text()
          .toLowerCase()
          .indexOf(value) > -1
      );
    });
  });

  function checkScreenSize() {
    let newWindowWidth = $(window).width();
    if (!isModalEnabled && newWindowWidth < MAX_WIDTH) {
      productsTableLines.forEach(function (line) {
        line.setAttribute("data-toggle", "modal");
        line.setAttribute("data-target", "#actionsModal");
      });
      isModalEnabled = true;
    }
    else if (isModalEnabled && newWindowWidth >= MAX_WIDTH) {
      productsTableLines.forEach(function (line) {
        line.removeAttribute("data-toggle");
        line.removeAttribute("data-target");
      });
      isModalEnabled = false;
    }
  }

  function getPaginationSelectedPage(url) {
    var chunks = url.split('?');
    var querystr = chunks[1].split('&');
    var pg = 1;
    for (i in querystr) {
      var qs = querystr[i].split('=');
      if (qs[0] == 'page') {
        pg = qs[1];
        break;
      }
    }
    return pg;
  }

  $('#products').on('click', '.pagination a', function (e) {
    e.preventDefault();
    var pg = getPaginationSelectedPage($(this).attr('href'));

    $.ajax({
      url: '/back-office/stock/ajax/products',
      data: { page: pg },
      success: function (data) {
        $('#products').html(data);
      }
    });
  });

  $('#categories').on('click', '.pagination a', function (e) {
    e.preventDefault();
    var pg = getPaginationSelectedPage($(this).attr('href'));

    $.ajax({
      url: '/back-office/stock/ajax/categories',
      data: { page: pg },
      success: function (data) {
        $('#categories').html(data);
      }
    });
  });

  $('#products').load('/back-office/stock/ajax/products?page=1');
  $('#categories').load('/back-office/stock/ajax/categories?page=1');
});