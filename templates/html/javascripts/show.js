// TODO: error callbacks
// TODO: only allow editing one at a time
// TODO: autocomplete
// TODO: saving indicator (spinner)

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
        // any way to extend chain to multiple lines?
        // don't want one super-long line; don't want multiple selections of same thing
        $('#just_edited').mouseover(mouse_over).mouseout(mouse_out)
        $('#just_edited').append($('#metadata_controls').clone())
        $('#just_edited').removeAttr('id')
      }
    })
  }
  function unset_metadata() {
    // console.log($(this).parent().parent().children())
    // $.ajax({
    //         url: '/unset_metadata/' + pagename,
    //         data: {predicate: $(this).parent().parent().children('.predicate').html()},
    //         success: function(){
    //           $(this).parent().parent().remove()
    //         }
    //       })
  }
  function cancel_edit(element,original) {
    datum = $(this).parent().parent().parent()
    if(datum.attr('id') == 'adding_metadata') {
      datum.remove()
    } else {
      $(element).parent().parent().html($(original)).mouseover(mouse_over).mouseout(mouse_out).removeAttr('id')
    }
  }
  function mouse_over() {
    $(this).children('#metadata_controls').show()
    $(this).children('#metadata_controls').children('#edit_metadata_image').click(function(){
      // get values, swap for edit interface, fill with values
      datum = $(this).parent().parent()
      original = datum.html()
      predicate = datum.children('.predicate').html()
      object = datum.children('.object').html()
      datum.replaceWith($('#edit_metadata').clone().show().attr('id','editing_metadata'))
      $('#editing_metadata input[name="predicate"]').val(predicate)
      $('#editing_metadata input[name="object"]').val(object).focus()
      // save and cancel controls
      $('#save_metadata_image').click(save_metadata)
      $('#cancel_metadata_link').click(function(){
        cancel_edit(this,original)
      })
    })
    $(this).children('#metadata_controls').children('#delete_metadata_image').click(unset_metadata)
  }
  function mouse_out() {
    $(this).children('#metadata_controls').hide()
  }
  
  $('#add_first_metadata_link').click(function(){
    $(this).parent().hide()
    $('ul#metadata_list').show()
    $('#add_metadata').show()
    $('#add_metadata_link').click()
  })
  $('#add_metadata_link').click(function(){
    $('#edit_metadata').clone().show().insertBefore('#edit_metadata').attr('id','adding_metadata')
    $('#adding_metadata input[name="predicate"]').focus()
    $('#save_metadata_image').click(save_metadata)
    $('#cancel_metadata_link').click(cancel_edit)
  })
  $('li.datum').mouseover(mouse_over).mouseout(mouse_out).each(function(){
    $(this).append($('#metadata_controls').clone())
  })
  
  // edit description
  
  $('#description').dblclick(function(){
    original = $(this).html()
    $(this).removeAttr('title')
    $(this).load('/edit_description/' + pagename,function(){
      $('#save_description_button').click(function(){
        $('#description').load('/save/' + pagename,
          $('textarea[name="description"]').serializeArray(),
          function(){
            $('#description').attr('title','double-click to edit')
          })
      })
      $('#cancel_description_link').click(function(){
        $(this).html(original)
        $(this).attr('title','double-click to edit')
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
