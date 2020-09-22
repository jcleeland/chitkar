$(document).ready(function() {

    $('.queueBtn').click(function(){
        queueDialog.dialog("open");
        $('.ui-dialog-buttonpane button:contains("Prev")').button().hide();
        $('.ui-dialog-buttonpane button:contains("Queue")').button().hide();
        jQuery.ajax({
            async: false,
            url:'index.php?r=newsletters/queue&id='+$(this).attr('id'),
            cache:false,
            success:function(html){
                $('#queue_results').html(html);
            }
        });
        return false;
    });

    /**
    * The Queue Dialog Box
    */

    var queueDialog=$('#queue-dialog').dialog({
        autoOpen: false,
        buttons: {
            Prev: function() {
                if($('#page2').is(":visible")) {
                    $('#page1').show();
                    $('#page2').hide();
                    $('.ui-dialog-buttonpane button:contains("Prev")').button().hide();        
                }
                if($('#page3').is(":visible")) {
                    $('#page3').hide();
                    $('#page2').show();
                    $('.ui-dialog-buttonpane button:contains("Queue")').button().hide();
                    $('.ui-dialog-buttonpane button:contains("Next")').button().show();
                }
            },
            Queue: function() {
                if($('#page3').is(':visible')) {
                    $('#newsletters-form').submit();
                }
            },
            Next: function() {
                if($('#page2').is(":visible")) {
                    $('#page2').hide();
                    $('#page3').show();
                    $('.ui-dialog-buttonpane button:contains("Queue")').button().show();
                    $('.ui-dialog-buttonpane button:contains("Next")').button().hide();
        
        
                }
                if($('#page1').is(":visible")) {
                    $('#page1').hide();
                    $('#page2').show();
                    $('.ui-dialog-buttonpane button:contains("Prev")').button().show();
        
                }
            },
            Cancel: function() {
                $(this).dialog("close");
            },
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
        height: 650,
        width: 850,
    });
});