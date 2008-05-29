<?php
class facts_controller {
  function index() {
    pass_var('facts',$GLOBALS['db']->select('triples'));
  }
}
?>