<div id="page_metadata">
  <?php if($page->links_out): ?>
    <?php foreach($page->links_out as $link): ?>
      <span class="link_rel"><?php echo $link->predicate ?>:</span> 
      <span class="link_value">
        <?php echo $link->object->getLink() ?>
      </span>
      <br />
    <?php endforeach ?>
  <?php else: ?>
    <p class="edit_prompt">double click to add metadata</p>
  <?php endif ?>
</div>
  <div id="page_body">
    <?php if ($page->body): ?>
      <?php echo do_filters('page_body',$page->body) ?>
    <?php else: ?>
      <p class="edit_prompt">double click to add a description</p>
    <?php endif ?>
  </div>
<?php if($page->links_in): ?>
  <div id="page_links_in">
    <?php foreach ($page->links_in as $link): ?>
      <?php echo $link->predicate->name ?> of 
      <?php echo $link->subject->getLink() ?>
      <br />
    <?php endforeach ?>
  </div>
<?php endif ?>