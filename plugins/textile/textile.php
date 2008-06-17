<?php
include 'lib/textile.php';

function textilize($text) {
  $textile = new Textile();
  return $textile->TextileThis($text);
}

add_hook('page_description','textilize');
?>