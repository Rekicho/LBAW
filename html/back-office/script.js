$(document).ready(function () {
    $(window).on("resize", function (e) {
        checkScreenSize();
    });

    checkScreenSize();

    function checkScreenSize(){
        var newWindowWidth = $(window).width();
        const maxWidth = 481;
        if (newWindowWidth < maxWidth) {

            $('#products').on("click", '#productsTable tr', function(){
                $('#actionsModal').modal('toggle');
            });
        }
    }
});