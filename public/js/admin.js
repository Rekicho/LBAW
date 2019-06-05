$(document).ready(function () {
    $("#search-member").on("keyup", function () {
      var value = $(this)
        .val()
        .toLowerCase();
      $("#staffMemberTable tr").filter(function () {
        $(this).toggle(
          $(this)
            .text()
            .toLowerCase()
            .indexOf(value) > -1
        );
      });
    });
  });