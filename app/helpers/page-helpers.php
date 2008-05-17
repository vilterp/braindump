<?php
function print_meta($input,$withlinks=false) {
  if($input) {
    foreach($input as $predicate => $object) {
      if($withlinks) {
        echo "$predicate: ".pagelink($object)."<br />";
      } else {
        echo "$predicate: $object\n";
      }
    }
  }
}
function parse_meta($input) {
  $pairs = array();
  foreach(explode("\n",$input) as $line) {
    if(!empty($line)) {
      $pair = explode(':',$line);
      $pairs[trim($pair[0])] = trim($pair[1]);
    }
  }
  return $pairs;
}
function save_meta($name,$input) {
  BQL::query("unset $name");
  foreach($input as $key=>$value) {
    BQL::query("set $key of $name to $value");
  }
}
function pagelink($name) {
  return getLink($name,"pages/show/$name");
}
function pageurl($name) {
  return getURL("pages/show/$name");
}
?>