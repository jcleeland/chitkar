$(document).ready(function() {
    /**
    * JQUERY FUNCTIONS FOR SITE INDEX PAGE
    */

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