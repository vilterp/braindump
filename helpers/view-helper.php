<?php
function load_view($path) {
  global $format;
  $GLOBALS['view'] = "app/views/$format/$path";
}
?>