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
  function set($subject,$predicate,$object,$allow_multiple=false) {
    write_to_log("triple.php: setting $predicate of $subject to $object");
    // get id's
    $data = array(
      'predicate_id' => page::create_if_doesnt_exist($predicate),
      'subject_id' => page::create_if_doesnt_exist($subject),
      'object_id' => page::create_if_doesnt_exist($object)
    );
    if($allow_multiple) { // allow only one object for a given subject and predicate
      if(self::exists($data['subject_id'],$data['predicate_id'],$data['object_id'])) {
        $GLOBALS['db']->update('triples',$data,array(
          'subject_id' => $data['subject_id'],
          'predicate_id' => $data['predicate_id']
        ));
      } else {
        $GLOBALS['db']->insert('triples',$data);
      }
    } else { // allow setting multiple objects for one subject and predicate
      if(self::exists($data['subject_id'],$data['predicate_id'])) {
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
