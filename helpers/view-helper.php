<?php
function load_view($path) {
  global $format;
  global $controller;
  $GLOBALS['view'] = "app/views/$format/$controller/$path";
}
?>