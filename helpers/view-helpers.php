<?php
function load_view($path) {
  if(!strpos($path,'.php')) $path .= '.php';
  global $runtime;
  $runtime['view'] = PATH_TO_VIEWS."/$runtime[format]/$runtime[controller]/$path";
}
function no_layout() {
  $GLOBALS['runtime']['layout'] = false;
}
function pass_var($var,$value) {
  $GLOBALS[$var] = $value;
}
function load_partial($path) {
  global $runtime;
  if(!strpos($path,'.php')) $path .= '.php';
  include PATH_TO_VIEWS."/$runtime[format]/partials/$path";
}
?>