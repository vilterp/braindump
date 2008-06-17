<?php
// FIXME: is this at all appropriate or necessary...?
function parse_wiki_links($input) { // I need to learn regular expressions...
  $final = "";
  $split = explode("]]",$input);
  for($i=0; $i<count($split)-1; $i++) {
    $segment = $split[$i];
    $split2 = explode("[[",$segment);
    $final .= $split2[0];
    $page_name = $split2[1];
    $final .= page_link($page_name);
  }
  $final .= $split[count($split)-1];
  return $final;
}
add_filter('page_description','parse_wiki_links');
?>