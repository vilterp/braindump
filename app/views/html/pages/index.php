<?php if(!$pages): ?>
  <p>No pages. Make one by typing its name into the box on the right &raquo;</p>
<?php else: ?>
  <?php // TODO: trac-style custom query UI ?>
  <ul>
    <?php foreach ($pages as $page): ?>
      <li><?php echo getLink($page->name,"pages/show/$page->name") ?></li>
    <?php endforeach ?>
  </ul>
<?php endif ?>
