<?php if(!$pages): ?>
  <p>No pages. <?php echo getLink('make one &raquo;','pages/edit') ?></p>
<?php else: ?>
  <?php // TODO: trac-style custom query UI ?>
  <ul>
    <?php foreach ($pages as $page): ?>
      <li><?php echo getLink($page->name,"pages/show/$page->name") ?></li>
    <?php endforeach ?>
  </ul>
<?php endif ?>
