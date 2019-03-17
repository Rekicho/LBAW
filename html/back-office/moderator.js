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
            usersTableLines.forEach(function(line) {
                line.setAttribute("data-toggle", "modal");
                line.setAttribute("data-target", "#userActionsModal");
            });
            reportsTableLines.forEach(function(line) {
                line.setAttribute("data-toggle", "modal");
                line.setAttribute("data-target", "#reportActionsModal");
            });
            isModalEnabled = true;
        }
        else if (isModalEnabled && newWindowWidth >= MAX_WIDTH) {
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
});