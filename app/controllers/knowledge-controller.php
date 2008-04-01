<?php
class knowledge_controller {
  function index() {
    $GLOBALS['facts'] = $GLOBALS['db']->links->select();
    load_view('list.php');
  }
}
?>