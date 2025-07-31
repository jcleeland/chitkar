/**
* jQuery functions relating to external DB and SQL code generation
* 
* EXPECTS THE PAGE TO HAVE:
*  - a '#dialog-confirm' div
*  - a '#generatesql' button
*  - a '#testSQLbtn' button
*  - a '#testsqlresults' div (usually inside the dialog-confirm)
*  - a '#test_sql' hidden input containing the sql to be tested (to be filled out by parent form)
* @param selector
* @param context
* 
* @returns {jQuery.fn.init}
*/
function validateSql() {
    console.log('Testing SQL');
    console.log($('#test_sql').val());
    
    $('#sqlOK').val(0);
    $('#testsql_results').html("<center>Testing List validity...</center>");
    var postdata={sql:$('#test_sql').val()};
    jQuery.ajax({
        type: 'POST',
        async: false,
        url: 'index.php?r=ExternalDb/checkValidSql',
        data: postdata,
        cache: false,
        success:function(output) {
            if(output=='0') {
                $('#sqlOK').val('1');
                $('#testsql_results').html($('#testsql_results').html()+'<center>Tested OK</center>');    
            } else {
                $('#sqlOK').val(0);
                $('#testsql_results').html("<center><strong style='color: red'>Invalid SQL</strong><br />You will need to check and correct any errors before continuing. The error message was '<i>"+output+"</i>'</center>");
            }
        },
    });        
}

function runSql() {
    $('#testsql_results').html('<center>SQL Tested OK<br />Running SQL...</center>');
    var postdata={sql:$('#test_sql').val()};
    if(!postdata) {
        $('#testsql_results').html('There is no SQL to test');
    } else {
        jQuery.ajax({
            type: 'POST',
            async: false,
            url:'index.php?r=ExternalDb',
            data:postdata,
            cache:false,
            success:function(html){
                $('#testsql_results').html("<center style='color: green'>Valid SQL</center>"+html);
            }
        });
    }

}

function generateSql() {
        //Generate the SQL from info provided, then check if it's different
        //to what is in the #RecipientLists_sql input
        var edb_fields=$('#edb_fieldnames').val().split(',');
        var edb_tables=$('#edb_tables').val().split(',');
        var edb_joins=$('#edb_joins').val().split(',');
        var edb_fieldjoins=$('#edb_fieldjoins').val().split(',');
        var edb_sql_joins=$('#edb_sql_joins').val().split(',');
        var edb_sql_wheres=$('#edb_sql_wheres').val().split(',');
        var edb_sql_froms=$('#edb_sql_froms').val().split(',');
        var edb_sql_selects=$('#edb_sql_selects').val().split(',');
        var sql_fieldjoins='';
        var value_pairs=new Array();
        var i=0;
        $.each(edb_fields, function( index, value) {
            /* Work through each field which has a value, and place that into the SQL */
            var inputId=edb_tables[index]+'\\.'+value;
            console.log('Looking for #'+inputId);
            if($('#'+inputId).val()) {
                /* This field has a value */
                console.log('OK. We`re working on this one ('+$('#'+inputId).val()+')');
                edb_sql_wheres.unshift(edb_tables[index]+"."+value+" ILIKE '"+$('#'+inputId).val()+"'");  //ILIKE for 
                if(edb_sql_froms.indexOf(edb_tables[index]) < 0 && edb_joins[index] != "" && edb_fieldjoins[index] == "") {
                    edb_sql_froms.unshift(edb_tables[index]);
                }
                if(edb_fieldjoins[index]!="") {
                    //console.log(edb_fieldjoins);
                    console.log('Adding a join! '+edb_fieldjoins[index]);
                    sql_fieldjoins=sql_fieldjoins+'\n'+edb_fieldjoins[index];
                }
                //If there are any field joins, add them now
                
                //if(edb_sql_joins.indexOf(edb_tables[index]) < 0) {edb_sql_joins.unshift(edb_tables[index]);} 
                //alert('Join:'+edb_joins[index]);
                if(edb_joins[index]) {edb_sql_wheres.unshift(edb_joins[index]);}
                value_pairs[i]=value+':'+$('#'+inputId).val();
                i++;
            }
        });
        
        sql_select=edb_sql_selects.join(',\n ');
        sql_wheres=edb_sql_wheres.join(' AND\n ');
        sql_froms=edb_sql_froms.join(',\n ');
        sql_joins=edb_sql_joins.join('\n  ');
        final_value_pairs=value_pairs.join(';');
        
        newsql="SELECT "+sql_select+"\nFROM "+sql_froms+"\n  "+sql_fieldjoins+"\n "+sql_joins+"\n\nWHERE "+sql_wheres;
        var output=new Array();
        output[0]=final_value_pairs;
        output[1]=newsql;
        return output;
}

$(document).ready(function() {
    
    $('#testSQLbtn').click(function() {
        $('#testsql_results').html("");
        testSQLbutton.dialog("open");
        $('#testsql_results').html("<center>Testing SQL validity...</center>");
        
        validateSql();
        if($('#sqlOK').val()==1) {
            $('#testsql_results').html('Finished validity testing. Executing SQL.');
            runSql();
            if($('#testsqlcount')) {
                if($('#sqlcount')) {
                    $('#sqlcount').val($('#testsqlcount').val());
                }
            }
        } else {
            $('#testsql_results').html('Validity testing did not succeed. SQL `'+$('test_sql').val()+'` returned nothing.');

        }
        return false; 
    }); 
    
    /**
    * For pages with a "Recipients" link - not forms, but previews
    * 
    */
    $('.previewSqlBtn').click(function() {
        testSQLbutton.dialog("open");
        /** 
        *   Need to fill out the #test_sql with matching data
        * 
        */
        jQuery.ajax({
            async: false,
            url:'index.php?r=newsletters/getsql&id='+$(this).attr('id'),
            cache:false,
            success:function(html){
                $('#test_sql').val(html);
            }
        });
        runSql();
        if($('#testsqlcount')) {
            if($('#sqlcount')) {
                $('#sqlcount').val($('#testsqlcount').val());
            }
        }
        return false;        
    });

    
    $('#dialog-confirm').dialog({
        autoOpen: false,
        resizable: false,
        height: 240,
        width: 400,
        modal: true,
        buttons: {
            "Change SQL": function() {
                /**
                * The following function creates SQL out of the values entered
                * and is triggered by the "generatesql" button
                */
                $(this).dialog("close");
                
                $('#RecipientLists_sql').val(newsql);
                $('#RecipientLists_values').val(final_value_pairs);
                $('#test_sql').val(newsql);
                newsql="";
                        
                $("#step2").hide();
                $("#step3").show();
            },
            "Use original SQL": function () {
                 $(this).dialog("close");
                 $("#step2").hide();
                 $("#step3").show();
            },
            Cancel: function() {
                $(this).dialog("close");
            }
        }
    });
    
    
    /**
    * The TEST SQL Dialog Box
    */
    var testSQLbutton=$('#testsql').dialog({
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
        height: 500,
        width: 700,
    });

});  