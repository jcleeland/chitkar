$(document).ready(function() {
    /**
    * JQUERY FUNCTIONS FOR SITE INDEX PAGE
    */

    $(document).ready(function() {
        var xhrPool = [];

        $.ajaxSetup({
            beforeSend: function(jqXHR) { // before jQuery send the request we will push it to our array
                xhrPool.push(jqXHR);
            },
            complete: function(jqXHR) { // when some of the requests completed it will splice from the array
                var index = xhrPool.indexOf(jqXHR);
                if (index > -1) {
                    xhrPool.splice(index, 1);
                }
            }
        });

        function abortAjax() {
            $.each(xhrPool, function(idx, jqXHR) {
                jqXHR.abort();
            });
        }

        $('a').click(function() {
            abortAjax();
        });
    });
    
    
    
    $('#swapbutton1').click(function() {
        $('#readsbox').toggle('slow');
        $('#linksbox').toggle('slow');    
    });
    $('#swapbutton2').click(function() {
        $('#readsbox').toggle('slow');
        $('#linksbox').toggle('slow');    
    });    
    $('#swapbutton3').click(function() {
        $('#queuedbox').toggle('slow');
        $('#sentbox').toggle('slow');    
    });
    $('#swapbutton4').click(function() {
        $('#queuedbox').toggle('slow');
        $('#sentbox').toggle('slow');    
    });   
});