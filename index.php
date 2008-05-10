<?php
include 'core/common.php';
// decide which controller, action, ident
$runtime['url'] = parse_request($_SERVER['REQUEST_URI']);
include PATH_TO_ROUTES;
// decide which format
$runtime['format'] = parse_format(parse_request($_SERVER['REQUEST_URI'],false));
// revert to defaults if necessary
if(empty($runtime['controller'])) $runtime['controller']=$defaults['controller'];
if(empty($runtime['action'])) $runtime['action']='index';
// this will be included from wrapper.php
$runtime['view'] = PATH_TO_VIEWS."$runtime[format]/$runtime[controller]/$runtime[action].php";
$runtime['layout'] = PATH_TO_VIEWS."$runtime[format]/layout.php";
// FIXME: this next line is ugly...
if($runtime['action'] == 'list') $runtime['action']='all'; // 'list' is already a php function
// load, initialize the main class (if it's there and it's not a crazy url)
$controller_path = PATH_TO_CONTROLLERS."$runtime[controller]-controller.php";
if(file_exists($controller_path)) {
  include $controller_path;
  eval("\$main_controller = new $runtime[controller]"."_controller();");
  eval("\$main_controller -> $runtime[action]();");
} else {
  $runtime['notfound'] = true;
}
// get the show on the road
if($runtime['layout']) include $runtime['layout'];
// finish up
do_hooks('absolute_end');
?>