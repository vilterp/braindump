<?php
$GLOBALS['filters'] = array();
// ugh comes_after is a mess... don't use this too much though
function do_filters($filter_point,$text) {
  foreach($GLOBALS['filters'] as $filter)
    if($filter['filter_point'] == $filter_point)
      return call_user_func($filter['function_name'],$text);
}
function add_filter($filter_point,$function_name) {
  if(is_string($filter_point)) {
    $GLOBALS['filters'][] = array(
      'filter_point' => $filter_point,
      'function_name' => $function_name
    );
} else { // array of filter points
    foreach($filter_point as $point) add_filter($point,$function_name);
  }
}
function remove_filter($filtername) {
  for($i=0; $i<count($GLOBALS['filters']); $i++)  {
    if($GLOBALS['filters'][$i]['function'] == $functionname) {
      unset($GLOBALS['filters'][$i]);
    }
  }
}
?>