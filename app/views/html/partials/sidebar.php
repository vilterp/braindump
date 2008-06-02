<form method="get" action="<?php echo getURL('redirect') ?>">
  <input type="text" name="name" value="goto" onclick="if(this.value='goto'){this.value=''}">
</form>

<ul>
  <?php // print special pages ?>
  <?php foreach(scandir('special_pages') as $page): ?>
    <?php if(strpos($page,'.') != 0): ?>
      <?php $clean = strip_extension($page) ?>
      <li><?php echo getLink(ucwords($clean),"special/$clean") ?></li>
    <?php endif ?>
  <?php endforeach ?>
</ul>