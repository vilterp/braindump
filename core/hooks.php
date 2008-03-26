<?php
$GLOBALS['hooks'] = array();
function do_hooks($hooklist) {
  foreach($GLOBALS['hooks'] as $hook) {
    if($hook['hook'] == $hooklist) {
      eval($hook['function'].";");
    }
  }
}
function add_hook($hooklist,$functionname) {
  array_push($GLOBALS['hooks'],array("function"=>$functionname,"hook"=>$hooklist));
}
function remove_hook($functionname) {
  for($i=0; $i<count($GLOBALS['hooks']); $i++)  {
    if($GLOBALS['hooks'][$i]['function'] == $functionname) {
      unset($GLOBALS['hooks'][$i]);
    }
  }
}
?>