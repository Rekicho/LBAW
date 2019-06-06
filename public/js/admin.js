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
        }
      });
    });
  
    $('#staff').load('/back-office/admin/ajax/staffMembers?page=1');
  });