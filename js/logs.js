$(document).ready(function() {
    /**
    * Scroll both divs to the bottom, after load
    */
    $("#statslog").animate({scrollTop: $('#statslog').prop("scrollHeight")}, 500);
    $("#queuelog").animate({scrollTop: $('#queuelog').prop("scrollHeight")}, 500);
    
});