<?php // FIXME: probably shouldn't be using <small> so much... ?>
<?php if (!$pages && empty($_GET['criteria'])): ?>
  <p class="notice">
    No pages. Make one by typing its name into the box on the right &raquo;
  </p>
<?php else: ?>
  <div id="criteria_box">
    <?php load_partial('criteria-box') ?>
  </div>
  <?php if ($pages): ?>
    <ul>
      <?php foreach ($pages as $page): ?>
        <li><?php echo page_link($page) ?></li>
      <?php endforeach ?>
    </ul>
  <?php elseif(!empty($_GET['criteria'])): ?>
    <p class="notice">No pages match your criteria.</p>
  <?php endif ?>
<?php endif ?>
