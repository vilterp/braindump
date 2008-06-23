<?php
function plugin_url($plugin,$url=NULL) {
  return base_url().PATH_TO_PLUGINS.$plugin.'/'.$url;
}
function load_plugin_js($plugin,$path) {
  if(!strpos($path,'.js')) $path .= '.js';
  echo "<script type='text/javascript' src='".plugin_url($plugin,$path)."'></script>\n";
}
function load_plugin_css($plugin,$path) {
  if(!strpos($path,'.css')) $path .= '.css';
  echo "<link rel='stylesheet' href='".plugin_url($plugin,$path)."'>\n";
}
// special pages
function get_special_pages() {
  $special_pages = array();
  foreach(dir_contents(PATH_TO_PLUGINS) as $plugin) {
    if(file_exists(PATH_TO_PLUGINS."$plugin/index.php")) {
      $special_pages[unhyphenate($plugin)] = $plugin;
    }
  }
  return $special_pages;
}
function special_page_link($special_page,$clean) {
  return get_link($special_page,"special/$clean");
}
?>