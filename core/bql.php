<?php
// FIXME: something like 'knights of columbus' would break setting and getting
// FIXME: error reporting on parse errors instead of putting in the wrong thing or doing nothing...
// FIXME: keep better track of datatypes
// FIXME: why am i using split and preg_split? what's the difference?
class BQL {
  function query($query) {
    if($GLOBALS['config']['keep_log']) write_to_log($query);
    $querysplit = explode(' ',$query);
    switch($querysplit[0]) { // first word
      case 'get':
        $params = split("(get | of )",$query);
        return self::_get($params[1],$params[2]);
        break;
      
      case 'set':
        $params = split("(set | of | to )",$query);
        $objects = english_to_array($params[3]);
        return self::_set($params[1],$params[2],$objects);
        break;
        
      case 'list':
        return self::_list(substr($query,11));
        break;
      
      case 'unset':
        $params = split("(unset | of )",$query);
        return self::_unset($params[1],$params[2]);
        break;
        
      case 'backlinks':
        return self::_backlinks(substr($query,13));
        
      case 'describe':
        $split = split("(describe | as )",$query);
        if(is_null($split[2])) {
          return self::_get_description($split[1]);
        } else {
          return self::_set_description($split[1],$split[2]);
        }
        
      case 'rename':
        $split = split("(rename | to )",$query);
        return self::_rename($split[1],$split[2]);
    }
  }
  function _get($predicate,$subject) {
    $subject_id = page::id_from_name($subject);
    if(is_singular($predicate)) { // eg. get color of apple
      $result = $GLOBALS['db']->select_column('triples','object_id',array(
        'subject_id' => $subject_id,
        'predicate_id' => page::id_from_name($predicate)
      ));
      if($result){return page::name_from_id($result);}else{return false;};
    } else { // eg. get parents of fidel castro
      $result = $GLOBALS['db']->select_column('triples','object_id',array(
        'subject_id' => $subject_id,
        'predicate_id' => page::id_from_name(singularize($predicate)) // parent
      ));
      $answers = array();
      foreach($result as $answer)
        $answers[] = page::name_from_id($answer);
      return $answers;
    }
  }
  function _set($predicate,$subject,$object) {
    if(is_array($object)) {
      foreach($object as $item)
        triple::set(singularize($predicate),$subject,$item,false);
      return true;
    } else {
      return triple::set($predicate,$subject,$object);
    }
  }
  function _list($conditions_string) {
    if(empty($conditions_string)) {
      return $GLOBALS['db']->select_column('pages','name','',array('order by'=>'name'));
    } else {
      $conditions = array();
      foreach(preg_split("/( and | or )/",$conditions_string) as $condition_string) {
        $condition = explode(' is ',$condition_string);
        $predicate_id = page::id_from_name($condition[0]);
        $object_id = page::id_from_name($condition[1]);
        $conditions[] = "(predicate_id=$predicate_id AND object_id=$object_id)";
      }
      // TODO: actually pay attention to 'and' and 'or' operators...
      $matches = $GLOBALS['db']->select_column('triples','subject_id',implode(' AND ',$conditions));
      if($matches) {
        $answers = array();
        foreach($matches as $match) {
          $answers[] = page::name_from_id($match);
        }
        return $answers;
      } else {
        return false;
      }
    }
  }
  function _unset($predicate,$subject) {
    // FIXME: should unset delete the record in the pages table as well?
    if(is_null($subject)) { // unset .
      $GLOBALS['db']->delete('triples',array(
        'subject_id' => page::id_from_name($predicate)
      ));
    } else { // unset . of .
      $GLOBALS['db']->delete('triples',array(
        'predicate_id' => page::id_from_name($predicate),
        'subject_id' => page::id_from_name($subject)
      ));
    }
    return true;
  }
  function _backlinks($name) {
    // backlinks to .
    $matches = $GLOBALS['db']->select('triples',array(
      'object_id' => page::id_from_name($name)
    ));
    $answers = array();
    if($matches) {
      foreach($matches as $match) {
        $predicate = page::name_from_id($match['predicate_id']);
        $subject = page::name_from_id($match['subject_id']);
        if(isset($answers[$predicate])) {
          if(is_array($answers[$predicate])) {
            $answers[$predicate][] = $subject;
          } else {
            $answers[$predicate] = array($answers[$predicate]);
            $answers[$predicate][] = $subject;
          }
        } else {
          $answers[$predicate] = $subject;
        }
      }
      return $answers;
    } else {
      return false;
    }
  }
  function _get_description($name) {
    $result = $GLOBALS['db']->select_one('pages','description',array('name'=>$name));
    if($result) {
      return $result;
    } else {
      return false;
    }
  }
  function _set_description($name,$description) {
    // FIXME: the DB field should be called 'description', not 'body'
    $GLOBALS['db']->update('pages',
      array('description'=>$description),
      array('name'=>$name)
    );
    return true;
  }
  function _rename($old_name,$new_name) {
    $GLOBALS['db']->update('pages',array('name'=>$new_name),array('name'=>$old_name));
    return true;
  }
}
?>
