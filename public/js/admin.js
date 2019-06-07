$(document).ready(function () {
  const MAX_WIDTH = 992;
  
  let staffTableLines;
  let isModalEnabled = false;

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

  $('#staff').on('click', '.pagination a', function (e) {
    e.preventDefault();
    let pg = getPaginationSelectedPage($(this).attr('href'));

    $.ajax({
      url: '/back-office/admin/ajax/staffMembers',
      data: { page: pg },
      success: function (data) {
        $('#staff').html(data);
        staffTableLines = document.querySelectorAll("#staffMemberTable > tr");
      }
    });
  });

  $('#staff').load('/back-office/admin/ajax/staffMembers?page=1', function () {
    staffTableLines = document.querySelectorAll("#staffMemberTable > tr");
    checkScreenSize();
  });

  $(window).on("resize", function (e) {
    checkScreenSize();
  });

  function checkScreenSize() {

    let newWindowWidth = $(window).width();
    if (!isModalEnabled && newWindowWidth < MAX_WIDTH) {
      staffTableLines.forEach(function (line) {
        line.setAttribute("data-toggle", "modal");
        line.setAttribute("data-target", "#staffActionsModal");
      });
      isModalEnabled = true;
    }
    else if (isModalEnabled && newWindowWidth >= MAX_WIDTH) {
      staffTableLines.forEach(function (line) {
        line.removeAttribute("data-toggle");
        line.removeAttribute("data-target");
      });
      isModalEnabled = false;
    }
  }
});