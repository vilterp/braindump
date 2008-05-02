<?php
class triple extends DatabaseObject {
  function connect() {
    $this->belongs_to('page','subject_id');
    $this->belongs_to('page','predicate_id');
    $this->belongs_to('page','object_id');
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
