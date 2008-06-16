<?php
include 'core/common.php';

// decide which controller, action, ident
$runtime['url'] = parse_request($_SERVER['REQUEST_URI']);
include PATH_TO_ROUTES;

// decide which format
$runtime['format'] = parse_format(parse_request($_SERVER['REQUEST_URI'],false));

// revert to defaults if necessary
if(!in_array($runtime['format'],scandir(PATH_TO_VIEWS)) || is_null($runtime['format']))
  $runtime['format'] = $defaults['format'];
if(empty($runtime['controller'])) $runtime['controller']=$defaults['controller'];
if(empty($runtime['action'])) $runtime['action']='index';

// check for hooks in init.php of each format
foreach(dir_contents(PATH_TO_VIEWS) as $format)
  if(file_exists(PATH_TO_VIEWS."/$format/init.php"))
    include PATH_TO_VIEWS."$format/init.php";
if(file_exists("$runtime[format]/init.php")) include "$runtime[format]/init.php";

// this will be included from layout.php
$runtime['view'] = PATH_TO_VIEWS."$runtime[format]/$runtime[controller]/$runtime[action].php";
$runtime['layout'] = PATH_TO_VIEWS."$runtime[format]/layout.php";

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
if($runtime['layout']) include $runtime['layout']; else include $runtime['view'];

// finish up
do_hooks('absolute_end');

// TODO: filter on the output buffer? (enable caching plugin...)
?>