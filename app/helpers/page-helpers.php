<?php
function pageLink($page_name,$link_to_edit_if_nonexistent) {
  if(page::exists($page_name)) {
    return getLink($page_name,"pages/show/".urlencode($page_name));
  } else {
    if($link_to_edit_if_nonexistent) {
      $url = "pages/edit/";
    } else {
      $url = "pages/show/";
    }
    return getLink($page_name,$url.urlencode($page_name),array('class'=>'non_existent_page'));
  }
}
function links_to($page_name) {
  $link = new link();
  return $link->find(array('to_page'=>$page_name),'',false);
}
function links_from($page_name) {
  $link = new link();
  return $link->find(array('from_page'=>$page_name),'',false);
}
?>