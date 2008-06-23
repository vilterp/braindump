// TODO: dynamic javascript.php a la Chyrp - plugins can add js to document.ready hook
$(document).ready(function(){
    // focus criteria input if visible
    if($('#criteria_form').css('display') != 'none') {
        $('#criteria_input').focus()
    }
    // show, focus criteria box when 'f' pressed
    $('#criteria_input').focus(function(){
        $('#criteria_form').show()
    })
    // toggle criteria box visibility, focus when 'filter' link clicked
    $('#criteria_box_toggle_link').click(function(){
        form = $('#criteria_form')
        if(form.css('display') == 'none') {
            form.show()
            $('#criteria_input').focus()
            $(this).html('Hide')
        } else {
            form.hide()
            $('#criteria_input').blur()
            $(this).html('Filter')
        }
    })
    // clear input on press of 'clear' link
    $('#criteria_box_clear_link').mousedown(function(){
        $('#criteria_input').attr('value','')
        $('#criteria_input').focus()
    })
    // hide 'clear' link when it's pressed
    $(document).mouseup(function(){
        if($('#criteria_input').value == '') {
            $('#criteria_box_clear_link').hide() // ?! FIXME: doesn't work
        }
    })
    // hide clear link if nothing to clear, show if there is
    $('#criteria_input').keyup(function(){
        clear_link = $('#criteria_box_clear_link')
        if(this.value == '') {
            clear_link.hide()
        } else {
            clear_link.show()
        }
    })
})