<p>
  <strong>page:</strong> <?php echo getLink($revision->page->name,'pages/show/'.$revision->page->name) ?>
  (<?php echo getLink('revisions','revisions/list/'.$revision->page->name) ?>) 
  <strong>time: </strong> <?php echo full_date($revision->time) ?>
</p>

<?php echo inline_diff($prev_revision->body,$revision->body,'\n') ?>