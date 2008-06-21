<?php
// general
function get_plugin_info($plugin) {
  global $runtime;
  return Spyc::YAMLLoad(PATH_TO_PLUGINS."$runtime[ident]/info.yaml");
}
// special pages
function get_special_pages() {
  $special_pages = array();
  foreach(dir_contents(PATH_TO_PLUGINS) as $plugin) {
    if(file_exists(PATH_TO_PLUGINS."$plugin/index.php")) {
      $special_pages[] = unhyphenate($plugin);
    }
  }
  return $special_pages;
}
function special_page_link($special_page) {
  return get_link(ucwords($special_page),"special/$special_page");
}
?>