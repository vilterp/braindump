<?php
function load_view($path) {
  global $format;
  global $controller;
  $GLOBALS['view'] = PATH_TO_VIEWS."/$format/$controller/$path";
}
// TODO: no_wrapper(), no_view() (for ajax etc...)
?>