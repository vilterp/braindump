<?php
class triple extends DatabaseObject {
  function connect() {
    $this->has_one('page','to_id','to_page');
    $this->has_one('page','from_id','from_page');
  }
  // helpers (urgh these are annoying)
  function exists($from_id,$rel,$to_id) {
    return $GLOBALS['db']->select_one(
      'triples',
      'id',
      "from_id=$from_id AND rel='$rel' AND to_id=$to_id"
    );
  }
}
?>
