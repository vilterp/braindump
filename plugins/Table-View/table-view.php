<?php
function load_tablesorter() {
  global $runtime;
  # FIXME: a method to check if the current plugin is active would be nice
  if($runtime['action'] == 'special' && $runtime['ident'] == 'Table View') {
    load_plugin_js('table-view','js/jquery.tablesorter.min.js');
    //load_js('jquery.jeditable.js');
    //load_plugin_js('table-view','js/jquery.cookie.js');
    //load_plugin_js('table-view','js/jquery.cookiejar.pack.js');
    //load_plugin_js('table-view','js/jquery.json.js');
    load_plugin_css('table-view','blue/style.css');
  }
}
add_hook('head','load_tablesorter');
?>