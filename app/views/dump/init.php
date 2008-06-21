<?php
// FIXME: should this just be hardcoded into pages/index.php?
function insert_dump_link() {
  global $pages;
  if($pages)
    echo get_link('dump this list &raquo;',"?format=dump&criteria=$_GET[criteria]",array('class'=>'control'))."\n";
}

add_hook('after_main_content','insert_dump_link');
?>