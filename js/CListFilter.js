//AJAX query for list view
$(document).ready(function() {
    var ajaxUpdateTimeout;
    var ajaxRequest;
    $('input#string, input#keyword').keyup(function(){
        ajaxRequest = $(this).serialize();
        if($(this).attr('id') !== 'keyword' && $('#keyword').length && $('#keyword').val().length > 0) {
            ajaxRequest = ajaxRequest+'&keyword='+$('#keyword').val();
        }
        var libraryVal = $('select#library').val();
        if(libraryVal) {
            ajaxRequest = ajaxRequest + '&library='+libraryVal;
        }
        clearTimeout(ajaxUpdateTimeout);
        ajaxUpdateTimeout = setTimeout(function () {
            $.fn.yiiListView.update(
                'ajaxListView',
                {data: ajaxRequest}
            );
            if($('#ajaxListView2').length) {
                $.fn.yiiListView.update('ajaxListView2', {data: ajaxRequest});
            }
        },
        300);
    });
    $('select#library').click(function() {
        var libVal = $(this).val();
        ajaxRequest = '';
        if(libVal) {
            ajaxRequest = 'library='+libVal;
        }
        if($('input#string').val().length > 0)
            ajaxRequest = ajaxRequest+(ajaxRequest ? '&' : '')+'string='+$('input#string').val();
        
        if($('#keyword').length && $('#keyword').val().length > 0)
            ajaxRequest = ajaxRequest+(ajaxRequest ? '&' : '')+'keyword='+$('#keyword').val();
        
        clearTimeout(ajaxUpdateTimeout);

        ajaxUpdateTimeout = setTimeout(function () {
            $.fn.yiiListView.update(
                'ajaxListView',
                {data: ajaxRequest}
            );
            if($('#ajaxListView2').length) {
                $.fn.yiiListView.update('ajaxListView2', {data: ajaxRequest});
            }
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