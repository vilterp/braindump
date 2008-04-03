<p>
  <strong>page:</strong> <?php echo getLink($revision->page->name,'pages/show/'.$revision->page->name) ?>
  (<?php echo getLink('revisions','revisions/list/'.$revision->page->name) ?>) 
  <strong>time: </strong> <?php echo full_date($revision->time) ?>
</p>

<div id="metadata_changes">
  <strong>metadata:</strong>
  <?php if(!$revision->links_set && !$revision->links_changed): ?>
    No changes
  <?php else: ?>
    <ul>
      <?php if ($revision->links_changed): ?>
        <?php foreach ($revision->links_changed as $link): ?>
          <li>
            changed <strong><?php echo $link->rel ?></strong> to 
            <strong><?php echo page::name_from_id($link->to_id) ?></strong>
          </li>
        <?php endforeach ?>
      <?php endif ?>
      <?php if ($revision->links_set): ?>
        <?php foreach ($revision->links_set as $link): ?>
          <li>
            set <strong><?php echo $link->rel ?></strong> to 
            <strong><?php echo page::name_from_id($link->to_id) ?></strong>
          </li>
        <?php endforeach ?>
      <?php endif ?>
    </ul>
  <?php endif ?>
</div>

<div id="body_changes">
  <strong>body:</strong>
  <?php echo inline_diff($prev_revision->body,$revision->body,'\n') ?>
</div>