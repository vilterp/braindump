<?php
class revisions_controller {
  function __construct() {
    $this->revision = new revision($GLOBALS['ident']);
  }
  function index() {
    global $revisions;
    if(!empty($GLOBALS['ident'])) { // specific page
      $revisions = $this->revision->find(
        array('page_id'=>page::id_from_name($GLOBALS['ident'])),
        array('order by'=>'time DESC')
      );
    } else {
      $revisions = $this->revision->find_all(array('order by'=>'time DESC'));
    }
    load_view('list.php');
  }
  function detail() {
    $GLOBALS['revision'] = $this->revision; // these are annoying
    $GLOBALS['prev_revision'] = $this->revision->find_one(
      "page_id = ".$this->revision->page_id." AND time < ".$this->revision->time,
      array('order by'=>'time DESC'),
      false
    );
  }
}
?>