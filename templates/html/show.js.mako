<script type="text/javascript" charset="utf-8">
  $(document).ready(function(){
    
    // edit metadata
    
    $('#edit_metadata_link').click(function(){
      if($('#metadata').hasClass('currently_editing')) {
        // save metadata
        this.innerHTML = 'Saving...' // spinner would be nice
        $('#metadata').load('${url("/save_metadata/%s" % escape(page["name"]))}',
                            $("textarea[name='metadata']").serializeArray(),
                            function(){
                              $('#edit_metadata_link').html('Edit')
                              $('#metadata').removeClass()
                            })
      } else {
        // swap metadata area for textarea
        this.innerHTML = 'Loading...'
        $('#metadata').addClass('currently_editing')
        $('#metadata').load('${url("/show/%s/metadata" % escape(page["name"]))}',
                            function(){
                              $('#edit_metadata_link').html('Save')
                              $("textarea[name='metadata']").focus()
                            })
      }
    })
    
    // edit description
    
    $('#edit_description_link').click(function(){
      if($('#description').hasClass('currently_editing')) {
        // save description
        this.innerHTML = 'Saving...' // spinner?
        $('#description').load('${url("/save_description/%s" % escape(page["name"]))}',
                               $("textarea[name='description']").serializeArray(),
                               function(){
                                 $('#edit_description_link').html('Edit')
                                 $('#description').removeClass()
                               })
      } else {
        // swap description for textarea
        this.innerHTML = 'Loading...'
        $('#description').addClass('currently_editing')
        $('#description').load('${url("/show/%s/description" % escape(page["name"]))}',
                               function(){
                                 $('#edit_description_link').html('Save')
                                 $("textarea[name='description']").focus()
                               })
      }
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
    $('#delete_yes').click(function(){
      confirm('Are you sure? The description and metadata will be lost.')
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
</script>
