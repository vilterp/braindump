<?php
include 'core/common.php';

// decide which format, controller, action, ident
$runtime['format'] = $_GET['format'];
$runtime['url'] = parse_request($_SERVER['REQUEST_URI']);
$runtime['entire_url'] = "http://$_SERVER[SERVER_NAME]$_SERVER[REQUEST_URI]";
include PATH_TO_ROUTES;

// revert to defaults if necessary
if(is_null($runtime['format'])) $runtime['format'] = $defaults['format'];
if(empty($runtime['controller'])) $runtime['controller'] = $defaults['controller'];
if(empty($runtime['action'])) $runtime['action'] = 'index';

// check for hooks in hooks.php of each format
foreach(dir_contents(PATH_TO_VIEWS) as $format)
  if(file_exists(PATH_TO_VIEWS."/$format/hooks.php"))
    include PATH_TO_VIEWS."$format/hooks.php";

// this will be included from layout.php
$runtime['view'] = PATH_TO_VIEWS."$runtime[format]/$runtime[controller]/$runtime[action].php";
$runtime['layout'] = PATH_TO_VIEWS."$runtime[format]/layout.php";

// load, initialize the main class
include PATH_TO_CONTROLLERS."$runtime[controller]-controller.php";
eval("\$main_controller = new $runtime[controller]"."_controller();");
eval("\$main_controller -> $runtime[action]();");

// get the show on the road
if($runtime['layout']) {
  include $runtime['layout']; 
} else {
  if(file_exists($runtime['view'])) include $runtime['view'];
}

// finish up
do_hooks('absolute_end');

// TODO: filter on the output buffer? (enable caching plugin...)
?>