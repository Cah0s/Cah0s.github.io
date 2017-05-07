idleTimer = null;
idleState = false;
//var time = 1 * 60; // 10 minutos para timeout
(function ($) {
    $(document).ready(function () {
        $('*').bind('mousemove keydown scroll', function () {
            var HOME = 'http://localhost/meineprojekte/teste/';
            clearTimeout(idleTimer);
            if (idleState === true) {
                // Reactivated event
                $.ajax({ url: HOME + 'inc/sesscheck.php?active' });
            }
            idleState = false;
            idleTimer = setTimeout(function () {
                // Idle Event
                idleState = true;
                $.ajax({ url: HOME + 'inc/sesscheck.php?inactive' });
            }, 5000);
            idleTimer = setTimeout(function () {
                // Idle Event
                idleState = true;
                $.ajax({ url: HOME + 'inc/sesscheck.php?outside' });
            }, 10000);
        });
        $("body").trigger("mousemove");
    });
}) (jQuery);