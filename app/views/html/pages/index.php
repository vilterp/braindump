<?php if (!$pages && empty($_GET['criteria'])): ?>
  <p class="notice">
    No pages. Make one by typing its name into the box on the right &raquo;
  </p>
  <p class="notice">
    Or, you can <?php echo getLink('import','import') ?> pages from a .dump.yaml file.
  </p>
<?php else: ?>
  <a href="#" id="criteria_box_toggle"><?php if($show_box) echo 'Hide'; else echo 'Filter'; ?></a>
  <div id="criteria_box"<?php if(!$show_box) echo ' style="display: none;"'; ?>>
    <form method="get" action="">
      <input type="text" name="criteria" value="<?php echo $_GET['criteria'] ?>" id="criteria" />
      <input type="submit" value="Update">
    </form>
  </div>
  <?php if ($pages): ?>
    <ul>
      <?php foreach ($pages as $page): ?>
        <li><?php echo pagelink($page) ?></li>
      <?php endforeach ?>
    </ul>
    <small>
      <?php echo getLink('dump this list &raquo;','dump.yaml?criteria='.$_GET['criteria']) ?>
    </small>
  <?php elseif(!empty($_GET['criteria'])): ?>
    <p class="notice">No pages match your criteria.</p>
  <?php endif ?>
<?php endif ?>
