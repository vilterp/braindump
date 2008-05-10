<?php
// views use this to make a header
// like pages/show/bla
// with each as a link
function get_app_path() {
  global $runtime;
  $path = array();
  array_push($path,getLink($runtime['controller'],$runtime['controller']));
  if($runtime['action'] != 'index') {
    if($runtime['action'] == 'all') $runtime['action'] = 'list'; // grr
    array_push($path,$runtime['action']);
  }
  if(!is_null($runtime['ident'])) {
    array_push($path,$runtime['ident']);
  }
  return $path;
}
?>