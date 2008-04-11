<?php
class facts_controller {
  function index() {
    $GLOBALS['facts'] = $GLOBALS['db']->select('triples');
  }
}
?>