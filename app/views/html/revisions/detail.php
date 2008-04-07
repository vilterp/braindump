<p>
  <strong>page:</strong> <?php echo getLink($revision->page->name,'pages/show/'.$revision->page->name) ?>
  (<?php echo getLink('revisions','revisions/list/'.$revision->page->name) ?>) 
  <strong>time: </strong> <?php echo full_date($revision->time) ?>
</p>

<div id="metadata_changes">
  <p><strong>metadata:</strong></p>
  <?php if(!$revision->triples_set): ?>
    No changes
  <?php else: ?>
    <?php
    $rels_changed = array();
    if($revision->triples_changed) {
      foreach($revision->triples_changed as $triple) {
        array_push($rels_changed,$triple->rel);
      }
    }
    ?>
    <ul>
      <?php if ($revision->triples_set): ?>
        <?php foreach ($revision->triples_set as $triple): ?>
          <li>
            <?php if (in_array($triple->rel,$rels_changed)): ?>
              changed <strong><?php echo $triple->rel ?></strong> to
              <strong><?php echo page::name_from_id($triple->to_id) ?></strong>
            <?php else: ?>
              set <strong><?php echo $triple->rel ?></strong> to 
              <strong><?php echo page::name_from_id($triple->to_id) ?></strong>
            <?php endif ?>
          </li>
        <?php endforeach ?>
      <?php endif ?>
    </ul>
  <?php endif ?>
</div>

<div id="body_changes">
  <p><strong>body:</strong></p>
  <?php echo inline_diff($prev_revision->body,$revision->body,'\n') ?>
</div>