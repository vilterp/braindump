<?php
include 'core/common.php';

$server = new IXR_Server(array(
  'graph.query' => 'Graph::query',
  'graph.get' => 'Graph::get',
  'graph.set' => 'Graph::set',
  'graph.unset' => 'Graph::_unset',
  'graph.list' => 'Graph::_list',
  'graph.backlinks' => 'Graph::backlinks',
  'graph.describe' => 'Graph::describe',
  'graph.rename' => 'Graph::rename',
  'graph.between' => 'Graph::between'
));

$server->serve();

?>