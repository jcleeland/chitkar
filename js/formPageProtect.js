jQuery(function($) {
/**
* Including this javascript in a page will force a message
* to appear if the user tries to click another link
* to leave. It should only be included with forms
* 
* 
*/
  $(window).bind("beforeunload", function(event) {
    return "Ok then"; 
  });
/**
* This unbinds the protection when submit is clicked, so the
* message isn't displayed on submitting the form
* 
*/
  $('form').submit(function() {
      $(window).unbind("beforeunload");
      submit();
  });

/**
* STOP THE Enter Button pressing submit, unless submit is visible
*   
*/
  jQuery.extend(
      jQuery.expr[ ":" ], 
      { reallyvisible : function (a) { return !(jQuery(a).is(':hidden') || jQuery(a).parents(':hidden').length); }}
  );
  $(window).keydown(function(event) {
    if( (event.keyCode == 13) && ($("input[name='yt0']:reallyvisible").size()===0) && !$(event.target).is("textarea")) {
        event.preventDefault();
        return false;
    } else if(event.keyCode == 13 && $("input[name='yt0']:reallyvisible").size()!==0){
        $("input[name='yt0']").click(); //Submit the form on enter, if step 4 is showing
    }
  });
});