<?php
// limits a string to a number of words, adds '...' at end
function limit_string($string,$words) {
  $split = explode(' ',$string);
  if(count($split) <= $words) return implode(' ',$split);
  $limited = array_chunk($split,$words);
  return implode(' ',$limited[0])."...";
}

function hyphenate($string) {
  return str_replace(' ','-',$string);
}
function unhyphenate($string) {
  return str_replace('-',' ',$string);
}

function underscoreize($string) {
  return str_replace(' ','_',$string);
}
function ununderscorize($string) {
  return str_replace('_',' ',$string);
}

// TODO: camelize/uncamelize?
?>