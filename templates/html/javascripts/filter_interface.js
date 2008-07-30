$(document).ready(function(){
  $('#visibility_toggle').click(function(){
    if($('#criteria_form').css('display') == 'none') {
      this.innerHTML = 'Hide'
      $('#criteria_form').show()
      $('#criteria_input').focus()
    } else {
      this.innerHTML = 'Filter'
      $('#criteria_form').hide()
      $('#criteria_input').blur()
    }
  })
  $('#criteria_form').submit(function(){
    $('#pages_list').load('/',$('#criteria').serialize())
  })
})