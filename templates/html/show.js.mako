<script type="text/javascript" charset="utf-8">
  $(document).ready(function(){

    $('#edit_metadata_link').click(function(){
      if($('#metadata').hasClass('currently_editing')) {
        // save metadata
        this.innerHTML = 'Saving...' // spinner would be nice
        $('#metadata').load('${url('/savemetadata/%s' % escape(page.name))}',
                            $("textarea[name='metadata']").serializeArray(),
                            function(){
                              $('#edit_metadata_link').html('Edit')
                              $('#metadata').removeClass()
                            })
      } else {
        // swap metadata area for textarea
        this.innerHTML = 'Save'
        $('#metadata').addClass('currently_editing')
        // do this with the DOM?
        $('#metadata').html('<textarea name="metadata" rows="8" cols="40"></textarea>')
        $("textarea[name='metadata']").load('${url('/show/%s/metadata' % escape(page.name))}')
      }
    })

  })
  
  // TODO: script rename and delete buttons
  
</script>