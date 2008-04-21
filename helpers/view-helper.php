<?php
function load_view($path) {
  global $runtime;
  if(!strpos($path,'.php')) $path .= '.php';
  $GLOBALS['view'] = PATH_TO_VIEWS."/$format/$runtime[controller]/$path";
}
function no_layout() {
  $GLOBALS['layout'] = false;
}
function set_var($var,$value) {
  $GLOBALS[$var] = $value;
}
function load_partial($path) {
  global $format;
  if(!strpos($path,'.php')) $path .= '.php';
  include PATH_TO_VIEWS."/$format/partials/$path";
}
?>