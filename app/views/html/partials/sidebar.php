<form method="get" action="<?php echo get_url('redirect') ?>">
  <input id="goto_box" type="text" value="goto" name="name">
</form>

<ul id="special_pages">
  <?php foreach(get_special_pages() as $special_page=>$clean): ?>
    <li class="special_page"><?php echo special_page_link(ucwords($special_page),$clean) ?></li>
  <?php endforeach ?>
</ul>
