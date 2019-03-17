$(document).ready(function () {

    $(window).on("resize", function (e) {
        checkScreenSize();
    });
    
    const MAX_WIDTH = 992;

    let isModalEnabled = false;
    let staffTableLines = document.querySelectorAll("#staffMemberTable > tr");

    checkScreenSize();

    function checkScreenSize() {
        
        let newWindowWidth = $(window).width();
        if (!isModalEnabled && newWindowWidth < MAX_WIDTH) {
            staffTableLines.forEach(function(line) {
                line.setAttribute("data-toggle", "modal");
                line.setAttribute("data-target", "#staffActionsModal");
            });
            isModalEnabled = true;
        }
        else if (isModalEnabled && newWindowWidth >= MAX_WIDTH) {
            staffTableLines.forEach(function(line) {
                line.removeAttribute("data-toggle");
                line.removeAttribute("data-target");
            });
            isModalEnabled = false;
        }
    }
});