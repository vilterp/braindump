// TODO: error callbacks
// TODO: autocomplete
// FIXME: if predicate is changed while editing, unset old predicate, then save new predicate & value

$(document).ready(function(){
  
  pagename = $('#page_name').html()
  
  // edit metadata
  
  function save_metadata() {
    controls = $('#editing_metadata, #adding_metadata').children('#metadata_form').children('#edit_controls')
    controls.replaceWith($('#saving_indicator').clone().show())
    $.ajax({
      url: '/save/' + pagename,
      data: $('#editing_metadata, #adding_metadata').children('#metadata_form').serialize(),
      success: function(response){
        $('#adding_metadata, #editing_metadata').replaceWith($(response).attr('id','just_edited'))
        $('#just_edited').mouseover(mouse_over).mouseout(mouse_out)
        $('#just_edited').append($('#metadata_controls').clone())
        $('#just_edited').removeAttr('id')
      }
    })
  }
  function unset_metadata() {
    datum = $(this).parent().parent()
    $.ajax({
      url: '/unset/' + pagename,
      data: {predicate: datum.children('.predicate').html()},
      success: function(){
        datum.remove()
      }
    })
  }
  function save_on_enter(event) {
    if(event.which == 13) { // other enter key?
      save_metadata()
    }
  }
  function cancel_edit(element,original) {
    datum = $(this).parent().parent().parent()
    if(datum.attr('id') == 'adding_metadata') {
      datum.remove()
    } else {
      datum = $(element).parent().parent()
      datum.html(original).mouseover(mouse_over).mouseout(mouse_out).removeAttr('id').addClass('datum')
      datum.children('#metadata_controls').hide()
    }
  }
  function cancel_any_edits() {
    currently_editing = $('#adding_metadata, #editing_metadata')
    currently_editing.children('#metadata_form').children('#edit_controls').children('#cancel_edit_link').click()
    $('#cancel_description_link').click()
  }
  function mouse_over() {
    $(this).children('#metadata_controls').show()
    $(this).children('#metadata_controls').children('#edit_metadata_image').click(function(){
      cancel_any_edits()
      // get values, swap for edit interface, fill with values
      datum = $(this).parent().parent()
      original = datum.html()
      predicate = datum.children('.predicate').html()
      object = datum.children('.object').html()
      datum.replaceWith($('#edit_metadata').clone().show().attr('id','editing_metadata'))
      $('#editing_metadata input[name="predicate"]').val(predicate)
      $('#editing_metadata input[name="object"]').val(object).focus()
      $('#editing_metadata input[name="predicate"], #editing_metadata input[name="object"]').keypress(function(e){
        save_on_enter(e)
      })
      // save and cancel controls
      $('#save_metadata_image').click(save_metadata)
      $('#cancel_edit_link').click(function(){
        cancel_edit(this,original)
      })
    })
    $(this).children('#metadata_controls').children('#unset_metadata_image').click(unset_metadata)
  }
  function mouse_out() {
    $(this).children('#metadata_controls').hide()
  }
  
  $('li.datum').mouseover(mouse_over).mouseout(mouse_out).each(function(){
    $(this).append($('#metadata_controls').clone())
  })
  $('#add_first_metadata_link').click(function(){
    $(this).parent().hide()
    $('ul#metadata_list').show()
    $('#add_metadata').show()
    $('#add_metadata_link').click()
  })
  $('#add_metadata_link').click(function(){
    cancel_any_edits()
    $('#edit_metadata').clone().show().insertBefore('#edit_metadata').attr('id','adding_metadata')
    $('#adding_metadata input[name="predicate"]').focus()
    $('#adding_metadata input[name="object"], #adding_metadata input[name="predicate"]').keypress(function(e){
      save_on_enter(e)
    })
    $('#save_metadata_image').click(save_metadata)
    $('#cancel_edit_link').click(cancel_edit)
  })
  
  // edit description
  
  $('#description').dblclick(function(){
    $('#adding_metadata, #editing_metadata').children('#metadata_controls').children('#cancel_description_link').click()
    cancel_any_edits()
    original = $(this).html()
    $(this).removeAttr('title')
    $(this).load('/edit_description/' + pagename,function(){
      $('textarea[name="description"]').focus()
      $('#save_description_button').click(function(){
        $('#description').load('/save/' + pagename,
          $('textarea[name="description"]').serializeArray(),
          function(){
            $('#description').attr('title','double-click to edit')
          })
      })
      $('#cancel_description_link').click(function(){
        $(this).parent().html(original)
        $(this).parent().attr('title','double-click to edit')
      })
    })
  })
  
  // delete link

  $('#delete_link').click(function(){
    $(this).hide()
    $('#delete_prompt').show()
  })
  $('#delete_no').click(function(){
    $('#delete_prompt').hide()
    $('#delete_link').show()
  })
  
  // rename link

  $('#rename_link').click(function(){
    $(this).hide()
    $('#rename_form').show()
    $("input[name='newname']").focus()
  })
  $('#rename_cancel').click(function(){
    $("input[name='newname']").blur()
    $('#rename_form').hide()
    $('#rename_link').show()
  })

})
