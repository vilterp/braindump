<?php
function print_metadata($input,$withlinks=false) {
  if($input) {
    foreach($input as $predicate => $object) {
      if($withlinks) {
        if(is_array($object)) {
          echo "$predicate: ".linked_page_list($object)."<br />";
        } else {
          echo "$predicate: ".page_link($object)."<br />";
        }
      } else {
        if(is_array($object)) {
          echo "$predicate: ".array_to_english($object)."\n";
        } else {
          echo "$predicate: $object\n";
        }
      }
    }
  }
}
function print_backlinks($input) {
  if($input) {
    foreach($input as $predicate=>$subject) {
      if(is_array($subject)) {
        echo "$predicate of ".linked_page_list($subject)."<br />";
      } else {
        echo "$predicate of ".page_link($subject)."<br />";
      }
    }
  }
}
function parse_metadata($input) {
  $pairs = array();
  foreach(explode("\n",$input) as $line) {
    if(!empty($line)) {
      $pair = explode(':',$line);
      $pairs[trim($pair[0])] = trim($pair[1]);
    }
  }
  return $pairs;
}
function save_metadata($name,$input) {
  BQL::_unset($name);
  foreach($input as $key=>$value) {
    if(is_plural($key)) $value = english_to_array($value); // multiple values
    BQL::set($name,$key,$value);
  }
}
function linked_page_list($pages) {
  $links = array();
  foreach($pages as $page) {
    $links[] = page_link($page);
  }
  return array_to_english($links);
}
function page_link($name) {
  return getLink($name,"show/$name");
}
function page_url($name) {
  return getURL("show/$name");
}
?>