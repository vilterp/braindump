<?php
function load_view($path) {
  global $format;
  global $controller;
  $GLOBALS['view'] = PATH_TO_VIEWS."/$format/$controller/$path";
}
function no_layout() {
  $GLOBALS['layout'] = false;
}
// TODO: no_wrapper(), no_view() (for ajax etc...)
?>