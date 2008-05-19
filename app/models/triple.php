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
  function set($predicate,$subject,$object,$update_if_exists=true) {
    $data = array(
      'predicate_id' => page::create_if_doesnt_exist($predicate),
      'subject_id' => page::create_if_doesnt_exist($subject),
      'object_id' => page::create_if_doesnt_exist($object)
    );
    // if this triple isn't already in the db, insert it
    if(self::exists($data['subject_id'],$data['predicate_id']) && $update_if_exists) {
      $GLOBALS['db']->update('triples',$data,array(
        'subject_id' => $data['subject_id'],
        'predicate_id' => $data['predicate_id']
      ));
    } else {
      $GLOBALS['db']->insert('triples',$data);
    }
    return true;
  }
}
?>
