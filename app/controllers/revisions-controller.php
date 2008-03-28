<?php
class revisions_controller {
  function __construct() {
    $this->revision = new revision($GLOBALS['ident']);
  }
  function index() {
    $this->all();
    load_view('list.php');
  }
  function all() {
    global $revisions;
    $revisions = $this->revision->findAll(array('order by'=>'time DESC'));
  }
  function detail() {
    $GLOBALS['revision'] = $this->revision; // these are annoying
  }
}
?>