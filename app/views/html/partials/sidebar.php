<form method="get" action="<?php echo get_url('redirect') ?>">
  <input id="goto_box" type="text" value="goto" name="name">
</form>

<ul id="special_pages">
  <?php foreach(get_special_pages() as $page): ?>
    <li class="special_page"><?php echo get_link($page,'special/'.hyphenate($page)) ?></li>
  <?php endforeach ?>
</ul>
