<?php
function redirect($url) {
  header('Location: '.getURL($url));
}
function mime_type($content_type) {
  header("Content-type: $content_type");
}
function force_download($filename='') {
  if(empty($filename)) {
    header('Content-Disposition: attachment');
  } else {
    header('Content-Disposition: attachment; filename="'.$filename.'"');
  }
}
?>