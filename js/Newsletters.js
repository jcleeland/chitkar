//Newsletter jQuery page management
$(document).ready(function() {

    /**
     * If any of the calendar event form elements change, update a hidden field called "icsPreviewContent" and "modelIcsContent"
     * with the updated .ics content. The "modelIcsContent" hidden field is used to save the .ics text content
     * to the database when the form is submitted.
     */
    $('#addCalendarEvent').change(function() {
        if ($(this).val() === 'yes') {
            $('#calendarEventFields').css('display', 'flex');
            updateIcsPreview(); //Update the preview and hidden field
        } else {
            $('#calendarEventFields').hide();
            $('#modelIcsContent').val(''); // Clear the hidden field
            $('#icsPreviewContent').text(''); // Clear the preview
        }
    });

    //If the start date & time in the calendar event page changes, make sure that the end date & time is later than it, and if not, change it to an hour later
    //$('#DummyModel_eventStart').on('blur', function() {
    $("#DummyModel_eventStart").on("change", function () {
    // Parse the start and end dates
        var startDate = new Date($('#DummyModel_eventStart').val());
        var endDate = new Date($('#DummyModel_eventEnd').val());

        // Check if endDate is not later than startDate
        if (endDate <= startDate) {
            $('#datewarning').fadeIn(1000);
            // Set endDate to one hour later than startDate
            var newEndDate = new Date(startDate.getTime() + 60*60*1000); // Add one hour

            // Manually format the date to "YYYY-MM-DD HH:MM:SS"
            var year = newEndDate.getFullYear();
            var month = ("0" + (newEndDate.getMonth() + 1)).slice(-2); // Months are 0-based
            var day = ("0" + newEndDate.getDate()).slice(-2);
            var hours = ("0" + newEndDate.getHours()).slice(-2);
            var minutes = ("0" + newEndDate.getMinutes()).slice(-2);
            var seconds = ("0" + newEndDate.getSeconds()).slice(-2);

            var newEndDateString = year + '-' + month + '-' + day + ' ' + hours + ':' + minutes + ':' + seconds;
            $('#DummyModel_eventEnd').val(newEndDateString);
            $('#datewarning').fadeOut(7000);
        }
    });
    // Bind the update function to the input fields
    $('#DummyModel_eventAttachmentType, #DummyModel_eventTitle, #DummyModel_eventStart, #DummyModel_eventEnd, #DummyModel_eventLocation, #DummyModel_eventDescription, #DummyModel_eventOrganiserName, #DummyModel_eventOrganiserEmail').on('input', updateIcsPreview);
    $('#DummyModel_eventStart, #DummyModel_eventEnd').on('change', updateIcsPreview);

    // Initial update
    initialiseIcsPreview();    
        
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
        console.log('Changing from page '+getCurrentPage());
        if(getCurrentPage()==1 && $('#Newsletters_title').val()=="") {
            alert('You must have a title');
        } else if(getCurrentPage()==4) {
            //Get the contents of the ckEditor
            const originalText = CKEDITOR.instances['Newsletters_content'].getData();
            //Check the contents of $('#newsletters_content') for safelinks and revert them
            //const originalText = $('#Newsletters_content').val();
            //console.log(originalText);
            
            const safelinksupdatedText = decodeSafelinks(originalText);

            //const otherupdatedText = decodeSafelinksV2(originalText);

            //console.log('New Text:', updatedText);

            const differences = compareStrings(originalText, safelinksupdatedText);
            //console.log('Initial Differences found:', differences);

            chitkarupdatedText = decodeChitkarWrapper(safelinksupdatedText);
            const differences2 = compareStrings(safelinksupdatedText, chitkarupdatedText);
            //console.log('Subsequent Differences found:', differences2);
            const updatedText=chitkarupdatedText;

            //Get the total length of the updatedText string
            console.log('Length of updatedText:', updatedText.length);

            // See if the length of updatedText is too much to store in a "Text" field in a mysql database
            if(updatedText.length > 65535) {
                alert('The length of the newsletter content is too long to store in the database. This usually happens because you\'ve copied and pasted something which contains pictures, and the picture has been encoded into the newsletter itself.\n\nPictures for newsletters should ONLY ever be inserted using the Chitkar Files system, and then inserting the picture using the Image button on the toolbar.\n\nPlease reduce the length of the content before proceeding. If the problem has been caused by pasted images in the newsletter, delete the images and insert them using the method described above.');
                return;
            }            

            if(differences.length > 0) {
                let changesSummary = 'The following changes were detected and made:\n\n';
                differences.forEach(diff => {
                    var niceOrig=decodeHTMLEntities(diff.original);
                    var niceUpdated=decodeHTMLEntities(diff.updated);
                    changesSummary += `*** Segment ${diff.segmentIndex}:**\n Original: ${niceOrig}\n\n Updated: ${niceUpdated}\n\n`;
                })
                if(confirm(`Chitkar detected the Microsoft "Safelinks" URL system in your newsletter and has converted that link back to the original URL.\n\n If you are happy to accept this change we recommend that you return to the last page before proceeding and double check the Meeting Link.\n\nProceed with the change?`)) {
                    $('#Newsletters_content').val(updatedText);
                    // Sync CKEditor with updated content
                    if (CKEDITOR.instances['Newsletters_content']) {
                        CKEDITOR.instances['Newsletters_content'].setData(updatedText);
                    }                    
                    console.log('Safe links have been decoded');
                    console.log('Changes made: ', differences);
                    console.log($('#Newsletters_content').val());
                    thispage=getCurrentPage()+1;
                    hideAll('page');
                    var pagename='page'+thispage;
                    $('#'+pagename).show();
                } else {
                    alert('No changes have been made, however you should note that URLs wrapped inside the Microsoft "Safelinks" system may not work for recipients. If you change your mind, go back to the newsletter editing page and click "Next" again.');
                    thispage=getCurrentPage()+1;
                    hideAll('page');
                    var pagename='page'+thispage;
                    $('#'+pagename).show();                
                }
            } else {
                thispage=getCurrentPage()+1;
                hideAll('page');
                var pagename='page'+thispage;
                $('#'+pagename).show();                     
            }

            
            //console.log($('#Newsletters_content').val());
        } else if(getCurrentPage()==5 && $('#addCalendarEvent').val()=="yes") {
            //Check to see if the calendar attachment option is selected, and if so, present a quick summary so that the user knows that they've set it up properly
            var ab_eventTitle=$('#DummyModel_eventTitle').val();
            var ab_startDate=$('#DummyModel_eventStart').val();
            var ab_endDate=$('#DummyModel_eventEnd').val();
            var ab_organiserName=$('#DummyModel_eventOrganiserName').val();
            var ab_organiserEmail=$('#DummyModel_eventOrganiserEmail').val();
            var ab_location=$('#DummyModel_eventLocation').val();
            /** Check ab_location for safelinks & remove if found */
            ab_location=decodeSafeLinksUrl(ab_location);
            if(ab_location != $('#DummyModel_eventLocation').val()) {
                // A change has been made to the location field, so we need to update it & the model
                $('#DummyModel_eventLocation').val(ab_location);
                updateIcsPreview();
            }
            
            var ab_description=$('#DummyModel_eventDescription').val();
            var ab_message="Event Title: "+ab_eventTitle+"\nEvent Date & Time: Starts "+ab_startDate+" and finishes "+ab_endDate+"\nEvent Organiser: "+ab_organiserName+ " ("+ab_organiserEmail+")\nEvent Location: "+ab_location+"\nEvent Description: "+ab_description;
            if(ab_eventTitle=='') {
                alert('You must have an event title!');
            } else if($('#DummyModel_eventStart').val()==$('#defaultstartdte').val()) {
                if(confirm('Your event start date and time is the same as the default entry - '+$('#DummyModel_eventStart').val()+' - confirm that this is intention in order to proceed')) {
                    if (confirm("You've chosen to include a calendar event file with this bulletin. Before proceeding, confirm that these details are correct:\n\n"+ab_message)) {
                        thispage=getCurrentPage()+1;
                        hideAll('page');
                        var pagename='page'+thispage;
                        $('#'+pagename).show();   
                    }   
                }
            } else if($('#DummyModel_eventEnd').val()==$('#defaultenddate').val()) {
                if(confirm('Your event end date and time is the same as the default entry - '+$('#DummyModel_eventEnd').val()+' - confirm that this is intention in order to proceed')) {
                    if (confirm("You've chosen to include a calendar event file with this bulletin. Before proceeding, confirm that these details are correct:\n\n"+ab_message)) {
                        thispage=getCurrentPage()+1;
                        hideAll('page');
                        var pagename='page'+thispage;
                        $('#'+pagename).show();
                    }    
                }
            } else {
                if (confirm("You've chosen to include a calendar event file with this bulletin. Before proceeding, confirm that these details are correct:\n\n"+ab_message)) {
                    thispage=getCurrentPage()+1;
                    hideAll('page');
                    var pagename='page'+thispage;
                    $('#'+pagename).show();
                }    
            }
        } else {
            thispage=getCurrentPage()+1;
            hideAll('page');
            var pagename='page'+thispage;
            $('#'+pagename).show();
            if(thispage==4) {
                var sqltext="";
                sqltext=$('#Newsletters_recipientSql').val();
                //console.log(sqltext);
                //Make it all lowercase
                sqltext=sqltext.toLowerCase();
                //Get the text between "select" and "from"
                sqltext=sqltext.substring(sqltext.indexOf("select")+6, sqltext.indexOf("from oms"));
                //console.log('SELECT STUFF - '+sqltext);
                //split the text by commas
                sqltext=sqltext.split(',');
                //grab the last word (after space) in each one
                var words='';
                for (var i=0; i<sqltext.length; i++) {
                    //console.log(sqltext[i]);
                    var y=sqltext[i];
                    //Check if the segment contains a bracket
                    var x=y.split(" ");
                    x=x.filter(item => !item.includes("(") && !item.includes(")"));
                    //console.log('X Value:');
                    //console.log(x);
                    //console.log('Doing '+x);
                    //console.log('Word is '+x[x.length-1]);
                    if(x[x.length-1].length > 0) {
                        words+="{"+x[x.length -1]+"} ";
                    }
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


function decodeSafelinks(text) {
    console.log('Decoding safelinks in href attributes:');

    // Regex to match Safe Links inside href="..."
    const hrefSafelinkRegex = /(href=")(https?:\/\/[\w.-]+\.safelinks\.protection\.outlook\.com\/\S*?\?url=([^&]+))(&[^"]*)?(")/gi;

    text = text.replace(hrefSafelinkRegex, function (match, beforeHref, fullSafelink, encodedUrl, remainingParams = '', afterHref) {
        try {
            let decodedUrl = decodeURIComponent(encodedUrl);

            // Handle nested encoding by decoding multiple times if necessary
            while (decodedUrl !== decodeURIComponent(decodedUrl)) {
                decodedUrl = decodeURIComponent(decodedUrl);
            }

            //console.log('Decoded URL:', decodedUrl);

            // Remove tracking parameters from the remaining part of the Safe Link
            remainingParams = remainingParams.replace(/(&amp;|&)?data=.*$/i, '') // Remove &data=
                                             .replace(/(&amp;|&)?sdata=.*$/i, '') // Remove &sdata=
                                             .replace(/(&amp;|&)?reserved=.*$/i, '') // Remove &reserved=
                                             .replace(/(&amp;|&)?nid=.*$/i, '') // Remove &nid=
                                             .replace(/(&amp;|&)?rid=.*$/i, '') // Remove &rid=
                                             .replace(/amp;/gi, ''); // Remove any remaining "amp;" junk

            // Decode &context JSON properly
            decodedUrl = decodedUrl.replace(/\?context=%7b.*?%7d/gi, function (match) {
                return decodeURIComponent(match);
            });

            // Replace the doublequotes in the URL with single quotes
            //decodedUrl = decodedUrl.replace(/"/g, "'");
            
            // Replace the doublequotes in the URL with %22
            decodedUrl = decodedUrl.replace(/"/g, '%22');

            // Construct the final href attribute with only the cleaned URL
            let finalHref = beforeHref + decodedUrl + remainingParams + afterHref;

            //console.log('Returning modified href:', finalHref);
            return finalHref;
        } catch (error) {
            console.error('Error decoding URL:', encodedUrl, error);
            return match; // Return the original Safe Link if decoding fails
        }
    });

    return text;
}

function decodeSafeLinksUrl(url) {
    console.log('Decoding Safe Links URL:', url);

    // Regex to match Safe Links
    const safelinkRegex = /https?:\/\/[\w.-]+\.safelinks\.protection\.outlook\.com\/\S*?\?url=([^&]+)/i;

    const match = url.match(safelinkRegex);
    if (!match) {
        // If no Safe Link is found, return the original URL
        //console.log('No Safe Link found. Returning original URL.');
        return url;
    }

    try {
        let decodedUrl = decodeURIComponent(match[1]);

        // Handle nested encoding by decoding multiple times if necessary
        while (decodedUrl !== decodeURIComponent(decodedUrl)) {
            decodedUrl = decodeURIComponent(decodedUrl);
        }

        //console.log('Decoded URL:', decodedUrl);

        // Remove tracking parameters from the remaining part of the Safe Link
        decodedUrl = decodedUrl.replace(/\?context=%7b.*?%7d/gi, function (match) {
            return decodeURIComponent(match);
        });

        // Replace the double quotes in the URL with %22
        decodedUrl = decodedUrl.replace(/"/g, '%22');

        console.log('Decoded Safelinks URL:', decodedUrl);
        return decodedUrl;
    } catch (error) {
        //console.error('Error decoding URL:', match[1], error);
        return url; // Return original URL if decoding fails
    }
}

function decodeChitkarWrapper(text) {
    //console.log('Decoding Chitkar Wrappers in href attributes:', text);
    
    var chitkarWrapper = document.getElementById('chitkarUrlWrapper').value;
    //console.log('ChitkarWrapper: ',chitkarWrapper);
    
    // Escape special characters in chitkarWrapper for regex (but don't modify the original variable)
    function escapeRegex(string) {
        return string.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
    }

    // Remove "http://" or "https://" from chitkarWrapper before using it in the regex
    var chitkarWrapperRegex = chitkarWrapper.replace(/https?:\/\//, ''); // Strip "http://" or "https://"
    chitkarWrapperRegex = escapeRegex(chitkarWrapperRegex);

    //console.log('Prepared chitkarWrapper:', chitkarWrapper);

    // Remove trailing slashes from regex version to prevent mismatches
    chitkarWrapperRegex = chitkarWrapperRegex.replace(/\\\/+$/, ''); 
    

    //console.log('Chitkar Wrapper (Regex Escaped):', chitkarWrapperRegex);

    // Updated Regex to match both uppercase & lowercase "url" and unencoded URLs
    const chitkarRegex = new RegExp(`href=["']((?:https?:\\/\\/)?${chitkarWrapperRegex}\\/links\\.php\\?(?:url|URL)=([^"']+))["']`, 'gi');

    console.log('Chitkar Regex:', chitkarRegex);

    let updatedText = text;
    let hasMatch = text.match(chitkarRegex);

    //console.log('Match Found:', hasMatch);

    while(hasMatch) {
        updatedText = updatedText.replace(chitkarRegex, (match, fullWrappedUrl, encodedUrl) => {
            let decodedUrl = decodeURIComponent(encodedUrl);
            console.log('Original match:', match);
            console.log('Full Wrapped Url before cleansing: ', fullWrappedUrl);
            while (decodedUrl !== decodeURIComponent(decodedUrl)) {
                decodedUrl = decodeURIComponent(decodedUrl);
            }
            // If the extracted URL lacks "http://" or "https://", add "https://"
            if (!/^https?:\/\//i.test(decodedUrl)) {
                decodedUrl = "https://" + decodedUrl;
            }

            // Remove tracking parameters after &nid=
            decodedUrl = decodedUrl.replace(/(&amp;|&)nid=.*$/, '');

            console.log('✅ Decoded and cleaned URL:', decodedUrl);
            
            //console.log('Url after cleansing: ', decodedUrl);

            //console.log('Match found after cleansing:', hasMatch);

            return `href="${decodedUrl}"`;

        });
        hasMatch = updatedText.match(chitkarRegex);
    }

    return updatedText;


    /////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // DEBUGGING: Check if regex finds a match BEFORE replace()
    /*let matchTest = text.match(chitkarRegex);
    console.log("DEBUG: Does the regex match? =>", matchTest);

    // If no match is found, log the regex separately in a manual test
    if (!matchTest) {
        console.error("❌ No match found! Possible issues:");
        console.error("- Check if chitkarWrapper is correct:", chitkarWrapper);
        console.error("- Check if the text contains a matching URL");
        console.error("- Test this regex manually with text.match(chitkarRegex)");
        return text; // Return unmodified text
    }

    // Replace the wrapped URL in the text while keeping the rest intact
    let updatedText = text.replace(chitkarRegex, (match, fullWrappedUrl, encodedUrl) => {
        let decodedUrl = decodeURIComponent(encodedUrl);

        console.log('Match Found:', match);
        console.log('Full Wrapped URL:', fullWrappedUrl);
        console.log('Extracted URL (Before Cleaning):', decodedUrl);

        // Handle multiple encoding layers if necessary
        while (decodedUrl !== decodeURIComponent(decodedUrl)) {
            decodedUrl = decodeURIComponent(decodedUrl);
        }

        // If the extracted URL lacks "http://" or "https://", add "https://"
        if (!/^https?:\/\//i.test(decodedUrl)) {
            decodedUrl = "https://" + decodedUrl;
        }

        // Remove tracking parameters after &nid=
        decodedUrl = decodedUrl.replace(/(&amp;|&)nid=.*$/, '');

        console.log('✅ Decoded and cleaned URL:', decodedUrl);
        
        // Return the <a href="cleanedURL">
        return `href="${decodedUrl}"`;
    });

    return updatedText; // Return the modified text instead of just the URL */
}



// Function to update the .ics preview & hidden field
function updateIcsPreview() {
    var eventAttachmentType = $('#DummyModel_eventAttachmentType').val();
    var eventTitle = $('#DummyModel_eventTitle').val();
    var dtStamp=$('#DummyModel_dtStamp').val();
    var eventStart = convertToUTCFormat($('#DummyModel_eventStart').val());
    var eventEnd = convertToUTCFormat($('#DummyModel_eventEnd').val());
    var eventLocation = $('#DummyModel_eventLocation').val();
    var eventDescription = $('#DummyModel_eventDescription').val();
    var eventOrganiserName = $('#DummyModel_eventOrganiserName').val();
    if(!eventOrganiserName) {
        eventOrganiserName="CPSU Victoria";
        $('#DummyModel_eventOrganiserName').val(eventOrganiserName);
    }
    var eventOrganiserEmail = $('#DummyModel_eventOrganiserEmail').val();
    if(!eventOrganiserEmail) {
        eventOrganiserEmail="enquiry@cpsuvic.org";
        $('#DummyModel_eventOrganiserEmail').val(eventOrganiserEmail);
    }
    var uid=$('#DummyModel_uid').val();
    if (!uid) {
        // Generate a random UID if the current value is empty
        var timestamp = new Date().getTime();
        var randomNum = Math.floor(Math.random() * 10000);
        uid = 'chitkar-' + timestamp + '-' + randomNum;
        $('#DummyModel_uid').val(uid);
        //console.log('Generated UID - '+uid);
    }

    // Remove safe links wrapping from URL fields
    if(eventLocation) eventLocation = decodeSafeLinksUrl(eventLocation);
    if(eventLocation != $('#DummyModel_eventLocation').val()) {
        $('#DummyModel_eventLocation').val(eventLocation);
    }
    if(eventDescription) eventDescription = decodeRawSafelinks(eventDescription);

    // Apply folding and escaping to the description
    var foldedDescription = foldAndEscapeLines(eventDescription);

    var icsContent = "BEGIN:VCALENDAR\r\n" +
                        "VERSION:2.0\r\n" +
                        "PRODID:-//CPSU SPSF Group Victorian Branch//Member Meetings//EN\r\n";
    if(eventAttachmentType == "msoutlook") {
        icsContent +=   "METHOD:REQUEST\r\n";
    }
    icsContent +=       "BEGIN:VTIMEZONE\r\n" +
                        "TZID:Australia/Melbourne\r\n" +
                        "BEGIN:STANDARD\r\n" +
                        "DTSTART:19700405T030000\r\n" +
                        "RRULE:FREQ=YEARLY;BYMONTH=4;BYDAY=1SU\r\n" +
                        "TZOFFSETFROM:+1100\r\n" +
                        "TZOFFSETTO:+1000\r\n" +
                        "TZNAME:AEST\r\n" +
                        "END:STANDARD\r\n" +
                        "BEGIN:DAYLIGHT\r\n" +
                        "DTSTART:19701004T020000\r\n" +
                        "RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=1SU\r\n" +
                        "TZOFFSETFROM:+1000\r\n" +
                        "TZOFFSETTO:+1100\r\n" +
                        "TZNAME:AEDT\r\n" +
                        "END:DAYLIGHT\r\n" +
                        "END:VTIMEZONE\r\n" +
                        "BEGIN:VEVENT\r\n" +
                        "UID:"+uid+"@cpsuvic.org\r\n" +
                        "DTSTAMP:"+ dtStamp + "\r\n" +
                        "DTSTART;TZID=Australia/Melbourne:" + eventStart + "\r\n" +
                        "DTEND;TZID=Australia/Melbourne:" + eventEnd + "\r\n" +
                        "SUMMARY:" + eventTitle + "\r\n" +
                        "ORGANIZER;CN=" + eventOrganiserName + ":mailto:" + eventOrganiserEmail + "\r\n" +
                        "ATTENDEE;CUTYPE=INDIVIDUAL;ROL=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION;RSVP=TRUE;CN=Attendee Name:mailto:attendee@email.com\r\n" +
                        "LOCATION:" + eventLocation + "\r\n" +
                        "DESCRIPTION:" + foldedDescription + "\r\n" +
                        "SEQUENCE:0\r\n" +
                        "TRANSP:OPAQUE\r\n" +
                        "END:VEVENT\r\n" +
                        "END:VCALENDAR";
    
    $('#icsPreviewContent').text(icsContent);
    $('#modelIcsContent').val(icsContent); // Update the hidden field
}

//Function to set up the ICS form on load (if a value already exists)
function initialiseIcsPreview() {
    var addCalendarEventValue = $('#addCalendarEvent').val();
    if (addCalendarEventValue === 'yes') {
        // If "Yes" is selected, display the calendar event fields and update the preview
        $('#calendarEventFields').css('display', 'flex');
        updateIcsPreview(); // Populate the .ics preview based on the model's values
    } else {
        // If "No" is selected, ensure the calendar event fields are hidden
        $('#calendarEventFields').css('display', 'none');
        $('#icsPreviewContent').text(''); // Clear the .ics preview
        $('#modelIcsContent').val(''); // Clear the hidden field for .ics content
    }
}

function convertToUTCFormat(dateTimeStr) {
    console.log('Converting Date:', dateTimeStr);

    // Extract components from the date-time string
    var parts = dateTimeStr.match(/(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)/);
    if (parts) {
        var year = parts[1];
        var month = parts[2].padStart(2, '0');
        var day = parts[3].padStart(2, '0');
        var hours = parts[4].padStart(2, '0');
        var minutes = parts[5].padStart(2, '0');
        var seconds = parts[6].padStart(2, '0');

        var formattedString = `${year}${month}${day}T${hours}${minutes}${seconds}`;
        console.log('Formatted Date:', formattedString);
        return formattedString;
    } else {
        console.error('Invalid date-time format');
        return null;
    }
}

// Function to escape new lines and fold lines for iCalendar format
function foldAndEscapeLines(text) {
    // Escape new lines
    var escapedText = text.replace(/\n/g, '\\n');

    // Fold lines longer than 75 characters
    var foldedText = '';
    var lineLength = 75;

    while (escapedText.length > lineLength) {
        var spaceIndex = escapedText.lastIndexOf(' ', lineLength);
        var breakIndex = spaceIndex > 0 ? spaceIndex : lineLength;
        foldedText += escapedText.substring(0, breakIndex).trim() + "\r\n ";
        escapedText = escapedText.substring(breakIndex).trim();
    }

    return foldedText + escapedText;
}



// Function to decode Safelinks urls
function decodeSafelinksOrig(text) {
    //console.log('Decoding safelinks', text);
    
    // Regex to match Safe Links URLs
    const safelinkRegex = /http[s]?:\/\/[\w.-]+\.safelinks\.protection\.outlook\.com\/\S*?\?url=([^&]+)&/gi;
    // Regex that will find the safedata URL parameters & remove them
    const safelinkRegexParams1 = /\&(amp;)?[s]?data=([^&(amp;)?]+)(?=&|$)/gi;
    const safelinkRegexParams2 = /\&(amp;)?reserved=([^&(amp;)?]+)(?=&|$)/gi;
    const safelinkRegex2 = /[\w.-]+\.cpsuvic\.org\/chitkar\/links\.php\?url=/gi;
    const safelinkRegex3 = /\&(amp;)?nid=([^&]+)(?=&|$)/gi;
    const safelinkRegex4 = /\&(amp;)?rid=([^&]+)(?=&|$)/gi;
    
    // Replace function to decode and replace Safe Links
    text=text.replace(safelinkRegex, function (match, encodedUrl) {
        //console.log('Match is', match)
        try {
            // Decode the encoded URL
            var decodedUrl = decodeURIComponent(encodedUrl);
            //console.log('Decoded URL:', decodedUrl);
            return decodedUrl;
        } catch (error) {
            //console.error('Error decoding URL:', encodedUrl, error);
            return match; // Return the original Safe Link if decoding fails
        }
    });

    
    // Remove the cpsuvic.org/chitkar/links.php?url part
    text = text.replace(safelinkRegex2, '');
    //console.log('linksphp - Output is', text);

    // Remove the safedata URL parameters
    text = text.replace(safelinkRegexParams1, function (match) {
        return match.replace(/&(amp;)?sdata=[^&]+/gi, '');
    });
    //console.log('Run 1a - Output is', text);
    text = text.replace(safelinkRegexParams2, function (match) {
        return match.replace(/&(amp;)?reserved=[^&]+(?=&|$)/gi, '');
    });
    //console.log('Run 1b - Output is', text);    
    
    // Remove the &nid= and &rid= parameters
    text = text.replace(safelinkRegex3, function (match) {
        return match.replace(/&nid=([^&]+)/gi, '');
    });
    //console.log('Run 3 - Output is', text);
    text = text.replace(safelinkRegex4, function (match) {
        return match.replace(/&rid=([^&]+)/gi, '');
    });
    //console.log('Run 4 - Output is', text);


    return text;
}

    
function compareStrings(original, updated) {
    // Split the HTML by tags or spaces for meaningful comparison
    const originalSegments = original.split(/(?<=>)|(?=<)/); // Split on tag boundaries
    const updatedSegments = updated.split(/(?<=>)|(?=<)/); // Split on tag boundaries
    const differences = [];

    for (let i = 0; i < Math.max(originalSegments.length, updatedSegments.length); i++) {
        const originalSegment = originalSegments[i] || '';
        const updatedSegment = updatedSegments[i] || '';
        if (originalSegment !== updatedSegment) {
            differences.push({
                segmentIndex: i + 1,
                original: originalSegment,
                updated: updatedSegment
            });
        }
    }

    return differences;
}

function decodeHTMLEntities(text) {
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = text;
    return tempDiv.textContent || tempDiv.innerText || '';
}
