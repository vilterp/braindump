<?php
class triple extends DatabaseObject {
  function connect() {
    $this->belongs_to('page','subject_id','subject');
    $this->belongs_to('page','predicate_id','predicate');
    $this->belongs_to('page','object_id','object');
  }
  // helpers (urgh these are annoying)
  function exists($subject_id,$predicate_id,$object_id) {
    return $GLOBALS['db']->select_one(
      'triples',
      'id',
      "subject_id=$subject_id AND predicate_id='$predicate_id' AND object_id=$object_id"
    );
  }
}
?>
