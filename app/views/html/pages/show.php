<?php if($page->in_db): ?>
  <p>
    <?php echo getLink('Edit',"pages/edit/$page->name",array('accesskey'=>'e')) ?> | 
    <?php echo getLink('Revisions',"revisions/list/$page->name") ?>
  </p>
  <?php echo do_filters('page_body',$page->body()) ?>
<?php else: ?>
  <p><em>This page doesn't exist yet. <?php echo getLink('change that &raquo;',"pages/edit/$page->name") ?></em></p>
<?php endif ?>