<?php
function include_dir($directory) {
  foreach(scandir("$directory/") as $file) {
    if(!strpos($file,".") == 0 && !is_dir($file)) {
      include "$directory/$file";
    }
  }
}
// get config
include 'app/config.php';
// get core
include_dir('core/');
// for timer, etc
do_hooks('absolute_beginning');
// get helpers
include_dir('helpers');
// connect to database
if(!empty($config['database'])) {
  $db = new Database($config['database'],$config['database_print_queries']);
}
// load application
include_dir('app/models');
include_dir('app/helpers');
// decide which controller, action, ident
$url = parse_request($config);
include 'app/routes.php';
// decide which format
if(!empty($_GET['format'])){
  $format = $_GET['format'];
} else {
  $format = $defaults['format'];
}
// revert to defaults if necessary
if(empty($controller)){$controller=$defaults['controller'];};
if(empty($action)){$action='index';};
// this will be included from wrapper.php
$view = "app/views/$format/$controller/$action.php";
if($action == 'list'){$action='all';}; // 'list' is already a php function
// load, initialize the main class
include "app/controllers/$controller-controller.php";
eval("\$main_controller = new $controller"."_controller();");
eval("\$main_controller -> $action();");
// get the show on the road
include "app/views/$format/layout.php";
// finish up
do_hooks('absolute_end');
?>