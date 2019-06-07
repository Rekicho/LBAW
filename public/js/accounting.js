$(document).ready(function () {

    $(window).on("resize", function (e) {
        checkScreenSize();
    });
    
    const MAX_WIDTH = 992;

    let isModalEnabled = false;
    let paymentsTableLines = document.querySelectorAll("#paymentsTable > tr");

    checkScreenSize();

    function checkScreenSize() {
        
        let newWindowWidth = $(window).width();
        if (!isModalEnabled && newWindowWidth < MAX_WIDTH) {
            paymentsTableLines.forEach(function(line) {
                line.setAttribute("data-toggle", "modal");
                line.setAttribute("data-target", "#paymentModal");
            });
            isModalEnabled = true;
        }
        else if (isModalEnabled && newWindowWidth >= MAX_WIDTH) {
            paymentsTableLines.forEach(function(line) {
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

    $('#payments').on('click', '.pagination a', function (e) {
        e.preventDefault();
        let pg = getPaginationSelectedPage($(this).attr('href'));
    
        $.ajax({
          url: '/back-office/accounting/ajax/payments',
          data: { page: pg },
          success: function (data) {
            $('#payments').html(data);
            addEventListeners();
          }
        });
      });

    $('#payments').load('/back-office/accounting/ajax/payments?page=1',addEventListeners);
});