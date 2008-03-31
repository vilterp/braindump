<?php
class knowledge_controller {
  function index() {
    $this->all();
    load_view('list.php');
  }
  function all() {
    $GLOBALS['facts'] = $GLOBALS['db']->links->select();
  }
}
?>