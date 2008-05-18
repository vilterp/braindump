<?php
class triple {
  function connect() {
    $this->belongs_to('page','subject_id','subject');
    $this->belongs_to('page','predicate_id','predicate');
    $this->belongs_to('page','object_id','object');
  }
  // helpers (urgh these are annoying)
  function exists($subject_id,$predicate_id) {
    $answer = $GLOBALS['db']->select(
      'triples',
      "subject_id=$subject_id AND predicate_id=$predicate_id"
    );
    if($answer) return true; else return false;
  }
}
?>
