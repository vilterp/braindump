<?php
class facts_controller {
  function index() {
    $GLOBALS['facts'] = $GLOBALS['db']->links->select();
    load_view('list.php');
  }
}
?>