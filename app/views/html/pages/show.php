<div id="page_metadata">
  <?php if ($page->links_out): ?>
    <?php print_meta($page->links_out,true) ?>
  <?php else: ?>
    <p class="edit_prompt">double click to add metadata</p>
  <?php endif ?>
</div>
  <div id="page_body">
    <?php if($page->description): ?>
      <?php echo do_filters('page_body',$page->description) ?>
    <?php else: ?>
      <p class="edit_prompt">double click to add a description</p>
    <?php endif ?>
  </div>
<?php if($page->links_in): ?>
  <div id="page_links_in">
    <?php print_backlinks($page->links_in) ?>
  </div>
<?php endif ?>