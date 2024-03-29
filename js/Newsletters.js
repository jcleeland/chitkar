//Newsletter jQuery page management
$(document).ready(function() {
    
    $('#Newsletters_recipientListsId').chosen();
    
    /** If the Newsletters_recipientSql textarea exists, and it has a value,
    *   populate the #test_sql input with the same value
    **/
    if($('#Newsletters_recipientSql').length && $('#test_sql').length) {
        if($('#Newsletters_recipientSql').val() != "") {
            $('#test_sql').val($('#Newsletters_recipientSql').val());
        }    
    }
    
    /**
    * PAGE MANAGEMENT
    * When page first loads, hide all page divs containing matching text in ID except page 1
    */
    hideAll('page');
    $('#page1').show();
    
    function hideAll(idname) {
        $('.'+idname).each(function(index) {
            $(this).hide();
        });
    }
    
    function getCurrentPage() {
        var thispage=0;
        $('.page').each(function(index) {
            if($(this).is(':visible')) {
                thispage=index+1;
            }                       
        });
        return thispage;        
    }
    $('.nextBtn').click(function(){
        if(getCurrentPage()==1 && $('#Newsletters_title').val()=="") {
            alert('You must have a title');
        } else {
            thispage=getCurrentPage()+1;
            hideAll('page');
            var pagename='page'+thispage;
            $('#'+pagename).show();
            if(thispage==4) {
                var sqltext="";
                sqltext=$('#Newsletters_recipientSql').val();
                console.log(sqltext);
                //Make it all lowercase
                sqltext=sqltext.toLowerCase();
                //Get the text between "select" and "from"
                sqltext=sqltext.substring(sqltext.indexOf("select")+6, sqltext.indexOf("from oms"));
                //split the text by commas
                sqltext=sqltext.split(',');
                //grab the last word (after space) in each one
                var words='';
                for (var i=0; i<sqltext.length; i++) {
                    console.log(sqltext[i]);
                    var y=sqltext[i];
                    var x=y.split(" ");
                    words+="{"+x[x.length -1]+"} ";
                };
                $('#replacementFields').html("The following replacement fields are available: <br />"+words);    
            }
        }
    });
    $('.prevBtn').click(function(){
        thispage=getCurrentPage()-1;
        hideAll('page');
        var pagename='page'+thispage;
        $('#'+pagename).show();
    });
    /**
    * END OF PAGE MANAGEMENT
    */
    
    $('#copySQLbtn').click(function() {
        var copyText=document.getElementById('Newsletters_recipientSql');
        copyText.select();
        copyText.setSelectionRange(0,99999); //For mobile devices
 
        $('#Newsletters_recipientListsId').val("");
        $('#Newsletters_recipientSql').val(copyText.value);
        $('#Newsletters_recipientValues').val("");
        $('#Newsletters_recipientSql').prop("disabled", false);
        $('#Newsletters_recipientValues').prop("disabled", false);
        $('#buildSQLbtn').show();
 
        //navigator.clipboard.writeText(copyText.value);
        //alert('SQL has been copied');   
    })
    
    if (!$('#Newsletters_recipientListsId').val()) {
        $('#Newsletters_recipientSql').prop("disabled", false);
        $('#Newsletters_recipientValues').prop("disabled", false);
    }
    $('#Newsletters_recipientListsId').change(function() {
        if(!$('#Newsletters_recipientListsId').val()) {
            $('#Newsletters_recipientSql').val("");
            $('#Newsletters_recipientValues').val("");
            $('#Newsletters_recipientSql').prop("disabled", false);
            $('#Newsletters_recipientValues').prop("disabled", false);
            $('#buildSQLbtn').show();
            
        } else {
            //Use AJAX to get the SQL & values of the selected recipientlist
            jQuery.ajax({
                type: 'POST',
                async: false,
                url: 'index.php?r=recipientLists/JsonOutput&id='+$('#Newsletters_recipientListsId').val(),
                cache: false,
                success:function(output) {
                    $('#Newsletters_recipientSql').val(output['sql']);
                    $('#Newsletters_recipientValues').val(output['values']);
                    $('#test_sql').val(output['sql']);
                },
            });    
            $('#Newsletters_recipientSql').prop("disabled", true);
            $('#Newsletters_recipientValues').prop("disabled", true);
            $('#buildSQLbtn').hide();
        }
    });
    $('#newsletters-form').submit(function() {
        //enable the sql fields for submission
        $('#Newsletters_recipientSql').prop("disabled", false);
        $('#Newsletters_recipientValues').prop("disabled", false);
        return true;        
    });

    $('#Newsletters_recipientSql').change(function(){
         $('#test_sql').val($('#Newsletters_recipientSql').val());
    });    

    /**
    * The TEST SQL Dialog Box
    */
    var buildSQLbutton=$('#buildsql').dialog({
        autoOpen: false,
        buttons: {
            Ok: function() {
                generation=generateSql();
                var newsql=generation[1];
                final_value_pairs=generation[0];
                $('#Newsletters_recipientSql').val(newsql);
                $('#test_sql').val(newsql);
                $('#Newsletters_recipientValues').val(final_value_pairs);
                /* VALIDATE BEFORE CLOSING? */
                $(this).dialog("close");
            },
            Cancel: function() {
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
        height: 400,
        width: 400,
    });
    $('#buildSQLbtn').click(function() {
        buildSQLbutton.dialog("open");
    });

});