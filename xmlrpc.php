<?php
include 'core/common.php';

function bql_query($querystring) {
  return BQL::query($querystring);
}

$server = new IXR_Server(array(
  'bql.query' => 'bql_query'
));
?>