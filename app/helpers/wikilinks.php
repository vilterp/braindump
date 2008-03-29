<?php
function parse_wiki_links($input) { // I need to learn regular expressions...
  $final = "";
  $split = split("}",$input);
  for($i=0; $i<count($split)-1; $i++) {
    $segment = $split[$i];
    $split2 = split("{",$segment);
    $final .= $split2[0];
    $page_name = $split2[1];
    if(page::exists($page_name)) {
      $final .= getLink($page_name,"pages/show/".urlencode($page_name));
    } else {
      $final .= getLink($page_name,"pages/edit/".urlencode($page_name),array('class'=>'non_existent_page'));
    }
  }
  $final .= $split[count($split)-1];
  return $final;
}
add_filter(array('page_body','comment_body'),'parse_wiki_links');
?>