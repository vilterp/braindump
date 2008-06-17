<div id="page_metadata">
  <?php if ($page->metadata): ?>
    <?php print_metadata($page->metadata,true) ?>
    <?php // FIXME add to page class? like $page->print_metadata(true) ?>
  <?php else: ?>
    <p class="notice">double click to add metadata</p>
  <?php endif ?>
</div>
  <div id="page_body">
    <?php if($page->description): ?>
      <?php echo do_filters('page_description',$page->description) ?>
    <?php else: ?>
      <p class="notice">double click to add a description</p>
    <?php endif ?>
  </div>
<?php if($page->backlinks): ?>
  <div id="page_links_in">
    <?php print_backlinks($page->backlinks) ?>
  </div>
<?php endif ?>