<?php
$GLOBALS['hooks'] = array();
function do_hooks($hook_point) {
  foreach($GLOBALS['hooks'] as $hook)
    if($hook['hook_point'] == $hook_point)
      call_user_func($hook['function_name']);
}
function add_hook($hook_point,$function_name) {
  $GLOBALS['hooks'][] = array(
    'hook_point' => $hook_point,
    'function_name' => $function_name
  );
}
function remove_hook($function_name) {
  for($i=0; $i<count($GLOBALS['hooks']); $i++)  {
    if($GLOBALS['hooks'][$i]['function_name'] == $function_name) {
      unset($GLOBALS['hooks'][$i]);
    }
  }
}
?>