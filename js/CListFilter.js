//AJAX query for list view
$(document).ready(function() {
    var ajaxUpdateTimeout;
    var ajaxRequest;
    $('input#string').keyup(function(){
        ajaxRequest = $(this).serialize();
        if($('select#library :selected').val() != '') {
            ajaxRequest = ajaxRequest + '&library='+$('select#library :selected').val();
        }
        clearTimeout(ajaxUpdateTimeout);
        ajaxUpdateTimeout = setTimeout(function () {
            $.fn.yiiListView.update(
            // this is the id of the CListView
                'ajaxListView',
                {data: ajaxRequest}
            )
        },
        // this is the delay
        300);
    });
    $('select#library').click(function() {
        ajaxRequest = $(this).serialize();
        if($('input#string').val().length > 0)
        ajaxRequest = ajaxRequest+'&string='+$('input#string').val();
        clearTimeout(ajaxUpdateTimeout);
        ajaxUpdateTimeout = setTimeout(function () {
            $.fn.yiiListView.update(
            // this is the id of the CListView
                'ajaxListView',
                {data: ajaxRequest}
            )
        }, 300);
    });
    
    $('input#recipid, input#recipemail, input#newslettersid').keyup(function() {
        ajaxRequest = $(this).serialize();
        clearTimeout(ajaxUpdateTimeout);
        ajaxUpdateTimeout = setTimeout(function () {
            $.fn.yiiListView.update(
            // this is the id of the CListView
                'ajaxListView',
                {data: ajaxRequest}
            )
        },
        // this is the delay
        300);   
    });
});