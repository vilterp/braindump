<?php function print_meta($links) { ?>
  <?php foreach($links as $link): ?>
    <span class="link_rel"><?php echo $link->rel ?>:</span> 
    <?php $page_name = page::name_from_id($link->to_id) ?>
    <span class="link_value"><?php echo getLink($page_name,"pages/show/$page_name") ?></span>
    <br />
  <?php endforeach ?>
<?php } ?>