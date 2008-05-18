<?php
class triple {
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
