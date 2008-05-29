<?php
// FIXME: something like 'knights of columbus' would break setting and getting
// FIXME: error reporting on parse errors instead of putting in the wrong thing or doing nothing...
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
        if(count($objects) > 1 && is_plural($params[1])) $params[3] = $objects;
        return self::_set($params[1],$params[2],$params[3]);
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
        return self::_describe($split[1],$split[2]);
        
      case 'rename':
        $split = split("(rename | to )",$query);
        return self::_rename($split[1],$split[2]);
        
      case 'between':
        $split = split("(between | and )",$query);
        return self::_between($split[1],$split[2]);
    }
  }
  function _get($predicate,$subject=NULL) {
    global $db;
    if(is_null($subject)) { // get .
      $subject_id = page::id_from_name($predicate);
      $result = $db->select('triples',array('subject_id'=>$subject_id));
      if($result) {
        $answers = array();
        foreach($result as $answer) {
          $predicate = page::name_from_id($answer['predicate_id']);
          $object = page::name_from_id($answer['object_id']);
          $answers[$predicate] = self::set_or_add($answers[$predicate],$object);
        }
        foreach($answers as $predicate=>$object) {
          if(is_array($object)) {
            $answers[pluralize($predicate)] = $answers[$predicate];
            unset($answers[$predicate]);
          }
        }
        return $answers;
      }
    } else { // get . of .
      $subject_id = page::id_from_name($subject);
      if(is_singular($predicate)) { // eg. get color of apple
        $result = $db->select_column('triples','object_id',array(
          'subject_id' => $subject_id,
          'predicate_id' => page::id_from_name($predicate)
        ));
        if($result){return page::name_from_id($result[0]);}else{return false;};
      } else { // eg. get parents of fidel castro
        $result = $db->select_column('triples','object_id',array(
          'subject_id' => $subject_id,
          'predicate_id' => page::id_from_name(singularize($predicate)) // parent
        ));
        $answers = array();
        if($result)
          foreach($result as $answer)
            $answers[] = page::name_from_id($answer);
        return $answers;
      }
    }
  }
  function _set($predicate,$subject,$object) {
    if(is_plural($predicate)) {
      foreach($object as $item)
        triple::set(singularize($predicate),$subject,$item,false);
      return true;
    } else {
      return triple::set($predicate,$subject,$object);
    }
  }
  function _list($conditions_string=NULL) {
    if(empty($conditions_string)) {
      return $GLOBALS['db']->select_column('pages','name','',array('order by'=>'name'));
    } else {
      $conditions = array();
      foreach(split("( and | or )",$conditions_string) as $condition_string) {
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
      if(is_plural($predicate)) $predicate = singularize($predicate);
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
        $answers[$predicate] = self::set_or_add($answers[$predicate],$subject);
      }
      return $answers;
    } else {
      return false;
    }
  }
  function _describe($name,$description=NULL) {
    global $db;
    if(is_null($description)) { // describe .
      $result = $db->select_one('pages','description',array('name'=>$name));
      if($result) {
        return $result;
      } else {
        return false;
      }
    } else { // describe . as .
      $db->update('pages',
        array('description'=>$description),
        array('name'=>$name)
      );
      return true;
    }
  }
  function _rename($old_name,$new_name) {
    $GLOBALS['db']->update('pages',array('name'=>$new_name),array('name'=>$old_name));
    return true;
  }
  // FIXME: what if there are multiple predicates...?
  function _between($one,$two) {
    $ids = array(
      page::id_from_name($one),
      page::id_from_name($two)
    );
    $result = $GLOBALS['db']->select_one('triples','predicate_id',
      "(subject_id = $ids[0] AND object_id = $ids[1]) OR ".
      "(subject_id = $ids[1] AND object_id = $ids[0])");
    if($result) {return page::name_from_id($result);}else{return false;};
  }
  function set_or_add($array,$var) {
    if(isset($array)) {
      if(is_array($array)) {
        $array[] = $var;
        return $array;
      } else {
        $array = array($array);
        $array[] = $var;
        return $array;
      }
    } else {
      return $var;
    }
  }
}
?>
