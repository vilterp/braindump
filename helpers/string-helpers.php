<?php
// limits a string to a number of words, adds ... at end
function limit_string($string,$words) {
  $split = explode(' ',$string);
  if(count($split) <= $words) return implode(' ',$split);
  $limited = array_chunk($split,$words);
  return implode(' ',$limited[0])."...";
}
function plural($string) {
  return $string.'s';
}
function singular($string) {
  return substr($string,0,strlen($string)-1);
}
?>