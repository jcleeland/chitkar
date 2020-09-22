$(document).ready(function() {
    /**
    * JQUERY FUNCTIONS FOR MANAGING PREVIEWS
    */
    
    $('.previewStatusBtn').click(function(){
        var update='#status'+$(this).attr('id');
        jQuery.ajax({
            async: false,
            url:'index.php?r=outgoings/status&id='+$(this).attr('id'),
            cache:false,
            success:function(html){
                $(update).show();
                $(update).html(html);
                setTimeout(function() {
                    $(update).fadeOut('slow');    
                }, 5000);
            }
        });
        return false;
    });

    $('.previewBtn').click(function(){
        previewDialog.dialog("open");    
        jQuery.ajax({
            async: false,
            url:'index.php?r=newsletters/preview&id='+$(this).attr('id'),
            cache:false,
            success:function(html){
                $('#preview_results').html(html);
            }
        });
        return false;
    });
    
    
    
    /**
    * The Preview Dialog Box
    */
    var previewDialog=$('#preview-dialog').dialog({
        autoOpen: false,
        buttons: {
            Ok: function() {
                $(this).dialog("close");
            }  
        },
        show: {
            effect: "drop",
            duration: 300
        },
        hide: {
            effect: "drop",
            duration: 500
        },
        modal: true,
        height: 700,
        width: 850,
    });

});