$(document).ready(function() {
    /**
    * JQUERY FUNCTIONS FOR MANAGING PREVIEWS
    */
    $('.showrecipients').click(function() {
        $('#recipientlist').toggle();
        $('#recipientlistclear').toggle();
        return false;
    });
    
    $('.showreadlist').click(function() {
        $('#readlist').toggle();
        $('#readreport').append('<div id="readlistspinner"><img src="/chitkar/images/ajax-loader.gif"></div>');
        if($('#readlist').is(':visible')) {
            jQuery.ajax({
                url:'index.php?r=outgoings/readlist&id='+$(this).attr('id'),
                cache:false,
                success:function(html){
                    $('#readreport').html(html);
                }
            });
        }
        return false;        
    });
    
    $('.showfaillist').click(function() {
        $('#faillist').toggle();
        if($('#faillist').is(':visible')) {
            jQuery.ajax({
                async: false,
                url:'index.php?r=outgoings/faillist&id='+$(this).attr('id'),
                cache:false,
                success:function(html) {
                    $('#failreport').html(html);
                }
            })
        }
    });
    $('.showlinklist').click(function() {
        $('#linklist').toggle();
        if($('#linklist').is(':visible')) {
            jQuery.ajax({
                async: false,
                url:'index.php?r=outgoings/linklist&id='+$(this).attr('id'),
                cache:false,
                success:function(html) {
                    $('#linkreport').html(html);
                }
            })
        }
    })

    $('#showreadtimes').show();
    /**
    * Google Visualizations don't like drawing to a hidden div, so
    * we need to hide the div in plain sight to start off with - by giving it a
    * negative z-index, so it is visible but behind everything else.
    * 
    * This means we have to do some tricky stuff to get the toggle working
    * as expected. If the div is visible but with a negative z-index, we hide it,
    * update the z-index to a positive number, then toggle it, so it appears
    * to the user like it's displaying for the first time. After that, normal
    * toggle works fine.
    */
    $('#showreadtimes').click(function() {
        if($('#chart_div').css("z-index") < 0) {
            $('#chart_div').hide();
            $('#chart_div').css("z-index", "500");
            $('#chart_div').toggle('slow');
        } else {
            $('#chart_div').toggle('slow');
        }
    });
    $('#chart_div').click(function(){
        $(this).toggle('slow');        
    })
});

function downloadInnerHtml(filename, elId, mimeType) {
    var elHtml = document.getElementById(elId).innerHTML;
    if (navigator.msSaveBlob) { // IE 10+ 
        navigator.msSaveBlob(new Blob([elHtml], { type: mimeType + ';charset=utf-8;' }), filename);
    } else {
        var link = document.createElement('a');
        mimeType = mimeType || 'text/plain';

        link.setAttribute('download', filename);
        link.setAttribute('href', 'data:' + mimeType  +  ';charset=utf-8,' + encodeURIComponent(elHtml));
        link.click(); 
    }
}

//When someone clicks on the "sort" button, we want to clear the #readreport div, and reload the readlist
// sending the extra url parameter "sort" to the controller. The element with the class ".updatereadlist"
// will also have a data-sortyby attribute, which will be used to determine the sort order.
function updatereadlist(id, sortby) {
    console.log('Updating readlist');
    $('#readreport').html('<div id="readlistspinner"><img src="/chitkar/images/ajax-loader.gif"></div>');
    console.log('Spinner should be visible');
    jQuery.ajax({
        url:'index.php?r=outgoings/readlist&id='+id+'&sort='+sortby,
        cache:false,
        success:function(html){
            $('#readreport').html(html);
        }
    });
    return false;
}