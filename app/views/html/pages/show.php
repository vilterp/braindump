<p id="page_controls">
  <?php echo getLink('Edit',"pages/edit/$page->name",array('accesskey'=>'e')) ?> | 
  <?php echo getLink('Revisions',"revisions/list/$page->name") ?>
</p>
<?php if($page->links): ?>
  <div id="page_metadata">
    <?php foreach($page->links as $link): ?>
      <span class="link_rel"><?php echo $link->rel ?>:</span> 
      <span class="link_value"><?php echo pagelink($link->to_page,false) ?></span>
      <br />
    <?php endforeach ?>
  </div>
<?php endif ?>
<?php if($page->in_db): ?>
  <div id="page_body">
    <?php echo do_filters('page_body',$page->body()) ?>
  </div>
<?php endif ?>
<?php if($page->links_to): ?>
  <div id="page_links_to">
    <?php foreach ($page->links_to as $link): ?>
      <?php echo $link->rel ?> of 
      <?php echo pagelink($link->from_page,"pages/show/$link->from_page") ?>
    <?php endforeach ?>
  </div>
<?php endif ?>