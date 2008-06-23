<?php
// load assets (css, js, images) easily
function asset_url($path) { // others rely on this
  return base_url()."assets/$path";
}
function load_css($path) {
  if(!strpos($path,'.css')) $path .= '.css';
  echo "<link rel='stylesheet' href='".asset_url("css/$path")."'>\n";
}
function load_js($path) {
  if(!strpos($path,'.js')) $path .= '.js';
  echo "<script type='text/javascript' src='".asset_url("js/$path")."'></script>\n";
}
function image($path,$options='') {
  return "<img src='".asset_url("images/$path")."'".html_options($options).">\n";
}
?>