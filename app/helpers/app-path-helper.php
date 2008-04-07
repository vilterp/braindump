<?php
// views use this to make a header
// like pages/show/bla
// with each as a link
function get_path() {
  global $controller, $action, $ident;
  $path = array();
  array_push($path,getLink($controller,$controller));
  if($action != 'index') {
    if($action == 'all') $action = 'list'; // grr
    array_push($path,$action);
  }
  if(!is_null($ident)) {
    array_push($path,$ident);
  }
  return $path;
}
?>