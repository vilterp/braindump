<?php
// limits a string to a number of words, adds ... at end
function limit_string($string,$words) {
  $split = explode(' ',$string);
  if(count($split) <= $words) return implode(' ',$split);
  $limited = array_chunk($split,$words);
  return implode(' ',$limited[0])."...";
}
function pluralize($string) {
  return $string.'s';
}
function singularize($string) {
  return substr($string,0,strlen($string)-1);
}
// 'apples, oranges, and milk' => array('apples','oranges','milk')
function english_to_array($sentence) {
  $final = array();
  $and_split = explode(' and ',$sentence);
  foreach(explode(',',$and_split[0]) as $item) {
    if(!empty($item)) $final[] = trim($item);
  }
  if($and_split[1]) $final[] = $and_split[1];
  return $final;
}
// array('apples','oranges','milk') => 'apples, oranges, and milk'
function array_to_english($array) {
  $final = "";
  for($i=0;$i<count($array)-1;$i++) {
    $final .= $array[$i].', ';
  }
  $final .= ' and '.$array[count($array)-1];
  return $final;
}
?>