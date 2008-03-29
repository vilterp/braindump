<?php if(!empty($ident)): ?>
  <p>Revisions for <?php echo getLink($ident,"pages/show/$ident") ?></p>
<?php endif ?>
<ul>
  <?php foreach ($revisions as $revision): ?>
    <li class="revision">
      <span class="revision_name"><?php echo getLink($revision->page->name,'revisions/detail/'.$revision->id) ?></span> 
      <span class="revision_time"><?php echo superTimeDiff($revision->time) ?></span>
    </li>
  <?php endforeach ?>
</ul>