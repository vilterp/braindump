<form method="get" action="<?php echo getURL('pages/redirect') ?>">
  <input type="text" name="name" value="goto" onclick="if(this.value='goto'){this.value=''}">
</form>
<ul>
  <li><?php echo getLink('New page &raquo;','pages/edit') ?></li>
  <li><?php echo getLink('Pages','pages') ?></li>
  <li><?php echo getLink('Revisions','revisions') ?></li>
  <li><?php echo getLink('Facts','facts') ?></li>
</ul>