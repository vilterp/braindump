<?php
$GLOBALS['filters'] = array();
$GLOBALS['filters_called'] = array();
$GLOBALS['come_afters'] = array();
// ugh comes_after is a mess... don't use this too much though
function do_filters($list,$text) {
  foreach($GLOBALS['filters'] as $filter) {
    if($filter['list'] == $list) {
      if(!is_null($filter['comes_after']) && !array_search($filter['comes_after'],$GLOBALS['filters_called'])) {
        // if the filter it comes after hasn't been called, add it to a list to be called when that function runs
        if(empty($GLOBALS['comes_after'][$filter['comes_after']])){$GLOBALS['come_afters'][$filter['comes_after']]=array();};
        array_push($GLOBALS['come_afters'][$filter['comes_after']],$filter['function']);
      } elseif(!array_search($filter['function'],$GLOBALS['filters_called'])) {
        // run filter function if it hasn't been run already
        $text = eval("return ".$filter['function']."(\$text);");
        // run filters that come after this
        $come_afters = $GLOBALS['come_afters'][$filter['function']];
        if(!is_null($come_afters)) {
          foreach($come_afters as $come_after) {
            $text = eval("return ".$come_after."(\$text);");
          }
        }
        // keep track of filters already called
        array_push($GLOBALS['filters_called'],$filter['function']);
      }
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