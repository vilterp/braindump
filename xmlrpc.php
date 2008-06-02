<?php
include 'core/common.php';

function bql_query($querystring){return BQL::query($querystring);}
function bql_get($s,$p=NULL){return BQL::get($s,$p);}
function bql_set($p,$s,$o){return BQL::set($p,$s,$o);}
function bql_unset($p,$s=NULL){return BQL::_unset($p,$s);}
function bql_list($criteria=NULL){return BQL::_list($criteria);}
function bql_backlinks($subject){return BQL::backlinks($subject);}
function bql_describe($subject,$desc=NULL){return BQL::describe($subject,$desc);}
function bql_rename($old,$new){return BQL::rename($old,$new);}
function bql_between($one,$two){return BQL::between($one,$two);}

$server = new IXR_Server(array(
  'bql.query' => 'bql_query',
  'bql.get' => 'bql_get',
  'bql.set' => 'bql_set',
  'bql.unset' => 'bql_unset',
  'bql.list' => 'bql_list',
  'bql.backlinks' => 'bql_backlinks',
  'bql.describe' => 'bql_describe',
  'bql.rename' => 'bql_rename',
  'bql.between' => 'bql_between'
));

$server->serve();

?>