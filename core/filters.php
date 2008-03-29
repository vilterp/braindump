<?php
$GLOBALS['filters'] = array();
// ugh comes_after is a mess... don't use this too much though
function do_filters($list,$text) {
  foreach($GLOBALS['filters'] as $filter) {
    if($filter['list'] == $list) {
      eval("\$text = $filter[function](\$text);");
    }
  }
  return $text;
}
function add_filter($filterlist,$functionname,$comes_after=NULL) {
  if(is_string($filterlist)) {
    // just a string of the filter list name
    array_push($GLOBALS['filters'],array("function"=>$functionname,"list"=>$filterlist,"comes_after"=>$comes_after));
  } else {
    // array of filter list names
    foreach($filterlist as $list) {
      array_push($GLOBALS['filters'],array("function"=>$functionname,"list"=>$list,"comes_after"=>$comes_after));
    }
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