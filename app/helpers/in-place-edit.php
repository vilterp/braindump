<?php if ($runtime['action'] == 'show'): ?>
  <?php load_js('jquery.jeditable') ?>
  <script type="text/javascript" charset="utf-8">
    $(document).ready(function() {
      $('#page_metadata').editable("<?php echo getURL('pages/save_meta/'.$page->name) ?>", {
        loadurl: "<?php echo getURL('pages/just_meta/'.$page->name) ?>",
        type: 'textarea',
        submit: 'Save',
        cancel: 'cancel',
        tooltip: 'double click to edit',
        event: 'dblclick',
        rows: 10,
        cols: 40
      })
      $('#page_body').editable("<?php echo getURL('pages/save_body/'.$page->name) ?>", { 
        loadurl: "<?php echo getURL('pages/just_body/'.$page->name)?>",
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
<?php endif ?>