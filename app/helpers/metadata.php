<?php
function parse_meta($input) {
  $pairs = array();
  foreach(explode("\n",$input) as $line) {
    $pair = explode(':',$line);
    array_push($pairs,
      array(
        'key' => trim($pair[0]),
        'value' => trim($pair[1])
      )
    );
  }
  return $pairs;
}
?>