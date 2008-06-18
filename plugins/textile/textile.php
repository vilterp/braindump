<?php
include 'lib/Textile.php';

function textilize($text) {
  $textile = new Textile();
  return $textile->TextileThis($text);
}

add_filter('page_description','textilize');
?>