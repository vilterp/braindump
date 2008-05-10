<?php
include 'core/common.php';

function query($querystring) {
  return BQL::query($querystring);
}

$server = new IXR_Server(array(
  'braindump.query' => 'query'
));
?>