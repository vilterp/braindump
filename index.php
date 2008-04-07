<?php
define('BD_VERSION','0.2');
// app paths
define('PATH_TO_CONFIG','app/config.php');
define('PATH_TO_ROUTES','app/routes.php');
define('PATH_TO_MODELS','app/models');
define('PATH_TO_VIEWS','app/views');
define('PATH_TO_CONTROLLERS','app/controllers');
define('PATH_TO_APP_HELPERS','app/helpers');
// system paths
define('PATH_TO_CORE','core/');
define('PATH_TO_HELPERS','helpers/');
define('PATH_TO_LIB','lib/');
function include_dir($directory) {
  foreach(scandir("$directory/") as $file) {
    if(!strpos($file,".") == 0 && !is_dir($file)) {
      require_once "$directory/$file";
    }
  }
}
// get core
include_dir(PATH_TO_CORE);
// for timer, etc
do_hooks('absolute_beginning');
// get helpers, lib
include_dir(PATH_TO_HELPERS);
include_dir(PATH_TO_LIB);
// get config
include PATH_TO_CONFIG;
// connect to database
if(!empty($config['database_path'])) {
  $db = new Database(
    $config['database_path'],$config['database_print_queries'],$config['database_cache_schema']
  );
}
// load application
include_dir(PATH_TO_MODELS);
include_dir(PATH_TO_APP_HELPERS);
// decide which controller, action, ident
$url = parse_request($config);
include PATH_TO_ROUTES;
// decide which format
if(!empty($_GET['format'])){
  $format = $_GET['format'];
} else {
  $format = $defaults['format'];
}
// revert to defaults if necessary
if(empty($controller)) $controller=$defaults['controller'];
if(empty($action)) $action='index';
// this will be included from wrapper.php
$view = PATH_TO_VIEWS."/$format/$controller/$action.php";
if($action == 'list') $action='all'; // 'list' is already a php function
// load, initialize the main class
include PATH_TO_CONTROLLERS."/$controller-controller.php";
eval("\$main_controller = new $controller"."_controller();");
eval("\$main_controller -> $action();");
// get the show on the road
include PATH_TO_VIEWS."/$format/layout.php";
// finish up
do_hooks('absolute_end');
?>