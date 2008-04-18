<?php function print_meta($links) { // for show.php ?>
  <?php foreach($links as $link): ?>
    <span class="link_rel"><?php echo $link->rel ?>:</span> 
    <?php $page_name = page::name_from_id($link->to_id) ?>
    <span class="link_value"><?php echo getLink($page_name,"pages/show/$page_name") ?></span>
    <br />
  <?php endforeach ?>
<?php } ?>
<?php
function parse_meta($input) {
  $pairs = array();
  foreach(explode("\n",$input) as $line) {
    $pair = explode(':',$line);
    array_push($pairs,
      array(
        'key' => trim($pair[0]),
        'value' => trim($pair[1])
      )
    );
  }
  return $pairs;
}
?>