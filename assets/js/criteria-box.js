$(document).ready(function(){
    $("#criteria_box_toggle_link").click(function(){
        form = $("#criteria_form")
        if(form.css('display') == 'none') {
            form.show()
            $("#criteria_input").focus()
            $(this).html("Hide")
        } else {
            form.hide()
            $(this).html("Filter")
        }
    })
    $("#criteria_box_clear_link").mousedown(function(){
        $('#criteria_input').attr('value','')
        $('#criteria_input').focus()
    })
    $(document).mouseup(function(){
        if($('#criteria_input').value == '') {
            $('#criteria_box_clear_link').hide() // ?! FIXME: doesn't work
        }
    })
    $("#criteria_input").keyup(function(){
        clear_link = $('#criteria_box_clear_link')
        if(this.value == '') {
            clear_link.hide()
        } else {
            clear_link.show()
        }
    })
})