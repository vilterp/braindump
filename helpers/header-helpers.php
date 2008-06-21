<?php
function redirect($url) {
  header('Location: '.get_url($url));
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
function flash($message) {
  session_start();
  $_SESSION[] = $message;
}
function get_flashes() {
  session_start();
  if($_SESSION) return $_SESSION; else return false;
}
?>