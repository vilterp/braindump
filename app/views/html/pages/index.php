<?php if($pages): ?>
  <?php // TODO: trac-style custom query UI ?>
  <ul>
    <?php foreach ($pages as $page): ?>
      <li><?php echo pagelink($page) ?></li>
    <?php endforeach ?>
  </ul>
<?php else: ?>
  <p class="notice">No pages. Make one by typing its name into the box on the right &raquo;</p>
<?php endif ?>
