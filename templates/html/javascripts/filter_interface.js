$(document).ready(function(){
  
  // toggle visibility of form when 'Filter|Hide' link clicked
  $('#visibility_toggle').click(function(){
    if($('#criteria_form').css('display') == 'none') {
      this.innerHTML = 'Hide'
      $('#criteria_form').show()
      $('#criteria_input').focus()
    } else {
      this.innerHTML = 'Filter'
      $('#criteria_input').blur()
      $('#criteria_form').hide()
    }
  })
  
  // clear criteria box when 'clear' link clicked
  $('#clear_link').click(function(){
    $('#criteria_input').attr('value','')
    $(this).hide()
    $('#criteria_input').focus()
  })
  
  // only show clear link when there's something to clear
  $('#criteria_input').keyup(function(){
    if(this.value == '') {
      $('#clear_link').hide()
    } else {
      $('#clear_link').show()
    }
  })
  
  // submit form, update list w/ ajax
  $('#criteria_form').submit(function(){
    $('#pages_list').hide()
    $('#spinner').show()
    // can't be doing this for everything. gotta automate it somehaw.
    $('#permalink').attr('href','?'+$('#criteria_form').serialize())
    $('#dump_link').attr('href','?format=dump&'+$('#criteria_form').serialize())
    
    $('#pages_list').load('/list',$('#criteria_form').serializeArray(),function(){
      $('#spinner').hide()
      $('#pages_list').show()
    })
    return false
  })
  
  // TODO: help button
  
})