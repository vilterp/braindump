$(document).ready(function(){
    $("#criteria_box_toggle").click(function(){
        box = $("#criteria_box");
        if(box.css('display') == 'none') {
            box.show();
            $("#criteria_input").focus();
            $(this).html("Hide");
        } else {
            box.hide();
            $(this).html("Filter");
        }
    });
})