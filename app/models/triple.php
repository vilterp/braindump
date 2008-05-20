<?php
class triple {
  // helpers (urgh these are annoying)
  function exists($subject_id,$predicate_id,$object_id=NULL) {
    $params = array(
      'subject_id' => $subject_id,
      'predicate_id' => $predicate_id,
    );
    if(!is_null($object_id)) $params['object_id'] = $object_id;
    $answer = $GLOBALS['db']->select('triples',$params);
    if($answer) return true; else return false;
  }
  function set($predicate,$subject,$object,$update_if_exists=true) {
    $data = array(
      'predicate_id' => page::create_if_doesnt_exist($predicate),
      'subject_id' => page::create_if_doesnt_exist($subject),
      'object_id' => page::create_if_doesnt_exist($object)
    );
    // if this triple isn't already in the db, insert it
    if($update_if_exists) {
      if(self::exists($data['subject_id'],$data['predicate_id'])) {
        $GLOBALS['db']->update('triples',$data,array(
          'subject_id' => $data['subject_id'],
          'predicate_id' => $data['predicate_id']
        ));
      } else {
        $GLOBALS['db']->insert('triples',$data);
      }
    } else {
      if(self::exists($data['subject_id'],$data['predicate_id'],$data['object_id'])) {
        $GLOBALS['db']->update('triples',$data,array(
          'subject_id' => $data['subject_id'],
          'predicate_id' => $data['predicate_id']
        ));
      } else {
        $GLOBALS['db']->insert('triples',$data);
      }
    }
    return true;
  }
}
?>
