<?php
function parse_wiki_links($input) { // I need to learn regular expressions...
  $final = "";
  $split = split("}",$input);
  for($i=0; $i<count($split)-1; $i++) {
    $segment = $split[$i];
    $split2 = split("{",$segment);
    $final .= $split2[0];
    $page_name = $split2[1];
    $final .= getLink($page_name,"pages/show/$page_name");
  }
  $final .= $split[count($split)-1];
  return $final;
}
add_filter(array('page_body','comment_body'),'parse_wiki_links');
?>