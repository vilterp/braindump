<script type="text/javascript" charset="utf-8">
  $(document).ready(function() {
    $('#page_metadata').editable("<?php echo get_url('save_metadata/'.urlencode($page->name)) ?>", {
      loadurl: "<?php echo get_url('just_meta/'.urlencode($page->name)) ?>",
      type: 'textarea',
      submit: 'Save',
      cancel: 'cancel',
      tooltip: 'double click to edit',
      event: 'dblclick',
      rows: 10,
      cols: 40
    })
    $('#page_body').editable("<?php echo get_url('save_description/'.urlencode($page->name)) ?>", { 
      loadurl: "<?php echo get_url('just_description/'.urlencode($page->name))?>",
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