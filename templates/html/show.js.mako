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
        $("textarea[name='metadata']").load('${url("/show/%s/metadata" % escape(page["name"]))}',
                                            function(){
                                              $('#edit_metadata_link').html('Save')
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
                               })
      }
    })
    
    // delete

    $('#delete_link').click(function(){
      $(this).addClass('currently_prompting')
      $(this).html('really? <a href="#" id="delete_yes">yes</a>/<a href="#" id="delete_no">no</a>')
      
      $('#delete_yes').attr('href','${url("/delete/%s" % page["name"])}')
      $('#delete_yes').click(function(){
        confirm('Are you sure? All metadata will be lost, as well as the description.')
      
      })
      
      $('#delete_no').click(function(){
        $('#delete_link').removeClass()
        $('#delete_link').html('delete')
      })
      
    })

  })
  
  // TODO: script rename button
  
</script>