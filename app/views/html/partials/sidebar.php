<form method="get" action="<?php echo get_url('redirect') ?>">
  <input id="goto_box" type="text" value="goto">
</form>

<ul id="special_pages">
  <?php foreach(get_special_pages() as $special_page): ?>
    <li class="special_page"><?php echo special_page_link($special_page) ?></li>
  <?php endforeach ?>
</ul>
