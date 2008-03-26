<?php
// load assets (css, js, images) easily
function assetURL($path) { // others rely on this
  return baseURL()."assets/$path";
}
function load_css($path) {
  echo "<link rel='stylesheet' href='".assetURL("css/$path")."'>\n";
}
function load_js($path) {
  echo "<script type='text/javascript' src='".assetURL("js/$path")."'></script>\n";
}
function image($path,$options='') {
  echo "<img src='".assetURL("images/$path")."' ".html_options($options).">\n";
}
?>