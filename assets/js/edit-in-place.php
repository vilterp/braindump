<script type="text/javascript" charset="utf-8">
  $(document).ready(function() {
    $('#page_metadata').editable("<?php echo getURL('pages/save_metadata/'.$runtime['ident']) ?>", {
      loadurl: "<?php echo getURL('pages/just_meta/'.$runtime['ident']) ?>",
      type: 'textarea',
      submit: 'Save',
      cancel: 'cancel',
      tooltip: 'double click to edit',
      event: 'dblclick',
      rows: 10,
      cols: 40
    })
    $('#page_body').editable("<?php echo getURL('pages/save_body/'.$runtime['ident']) ?>", { 
      loadurl: "<?php echo getURL('pages/just_body/'.$runtime['ident'])?>",
      type: 'textarea',
      submit: 'Save',
      cancel: 'cancel',
      tooltip: 'double click to edit',
      event: 'dblclick',
      rows: 20,
      cols: 60
    })
  });
</script>