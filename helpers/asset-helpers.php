<?php
// load assets (css, js, images) easily
function assetURL($path) { // others rely on this
  return baseURL()."assets/$path";
}
function load_css($path) {
  if(!strpos($path,'.css')) $path .= '.css';
  echo "<link rel='stylesheet' href='".assetURL("css/$path")."'>\n";
}
function load_js($path) {
  if(!strpos($path,'.css')) $path .= '.js';
  echo "<script type='text/javascript' src='".assetURL("js/$path")."'></script>\n";
}
function image($path,$options='') {
  return "<img src='".assetURL("images/$path")."' ".html_options($options).">\n";
}
?>