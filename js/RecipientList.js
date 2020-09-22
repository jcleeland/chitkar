//MUST Based Jquery To Build SQL
$(document).ready(function() {
    
   
    if($("#RecipientLists_name").val() != "") {
        $("#DisplayListName").html("<h2 style='text-decoration: underline'>"+$("#RecipientLists_name").val()+"</h2>");
    }

    
    /**
    * The following function hides step 1 and displays step 2
    */
    $('#entervalues').click(function() {
       $("#step1").hide();
       /**
       * Enter the values from the values field into the appropriate filter fields
       */
       if($("#RecipientLists_values").val() !== "") {
            //alert($("#RecipientLists_values").val()); 
            var newvalues=$('#RecipientLists_values').val().split(';');
            for (index=0; index < newvalues.length; ++index) {
                var thislot=newvalues[index].split(':');
                $('#'+thislot[0]).val(thislot[1]);
            }
            
       }
       /**
       * Update the listname field
       * 
       */
       $("#DisplayListName").html("<h2 style='text-decoration: underline'>"+$("#RecipientLists_name").val()+"</h2>");
       $("#step2").show();
       $("#step3").hide(); 
    });
    /**
    * The following function hides step 2 and displays step 1
    */
    $('#namekeywords').click(function() {
        $("#step2").hide();
        $("#step1").show();
    });

    $('#returnentervalues').click(function() {
       $("#step1").hide();
       $("#step2").show();
       $("#step3").hide(); 
    });
    
    $('#reviewlist').click(function() {
        validateSql();
        if($('#sqlOK').val()=='1') {
            $("#step3").hide();
            $("#step4").show();
        } else {
            alert("Your SQL does not validate. You need to correct it. Use the Test SQL button to review and find a description of the error.");
        }
    });
    
    $('#returnsql').click(function(){
        $('#step4').hide();
        $('#step3').show();
    });

    $('#generatesql').click(function() {
        var generation=generateSql();
        var newsql=generation[1];
        var final_value_pairs=generation[0];
        if($("#RecipientLists_sql").val() != "" && $("#RecipientLists_sql").val() != newsql) {
              $('#dialog-confirm').dialog("open");
        } else {
              $('#RecipientLists_sql').val(newsql);
              $('#test_sql').val(newsql);
              $('#RecipientLists_values').val(final_value_pairs);
              $("#step2").hide();
              $("#step3").show();
        }    
    });
    
    $('#RecipientLists_sql').change(function(){
         $('#test_sql').val($('#RecipientLists_sql').val());
    });
});