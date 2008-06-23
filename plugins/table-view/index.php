<?php  
$pages = array();
foreach(BQL::_list($_GET['criteria']) as $page) {
  $pages[$page] = BQL::get($page);
}

$attributes = array();
foreach($pages as $page) {
  foreach($page as $attr=>$value) {
    if(!in_array($attr,$attributes)) $attributes[] = $attr;
  }
}

function print_or_list_with_link($page) {
  if(is_array($page)) echo linked_page_list($page); else echo page_link($page);
}
?>
<script type="text/javascript">
$(document).ready(function(){
  $('.tablesorter').tablesorter()
})  
</script>
<form method='get' action=''>
  criteria: 
  <input type="text" name="criteria" value="<?php echo $_GET['criteria'] ?>"/>
  <input type="submit" value="Update &raquo;"/>
</form>
<table class="tablesorter">
  <thead>
    <tr>
      <th>Page Name</th>
      <?php foreach($attributes as $attr): ?>
        <th><?php echo $attr ?></th>
      <?php endforeach ?>
    </tr>
  </thead>
  <tbody>
    <?php foreach($pages as $name=>$data): ?>
      <tr>
        <td class="page_value"><?php echo page_link($name) ?></td>
        <?php foreach($attributes as $attr): ?>
          <td class="page_value"><?php print_or_list_with_link($data[$attr]) ?></td>
        <?php endforeach ?>
      </tr>
    <?php endforeach ?>
  </tbody>
</table>