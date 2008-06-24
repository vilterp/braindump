<?php
// FIXME: error reporting on parse errors instead of putting in the wrong thing or doing nothing...
class Graph {
  function query($query) {
    if($GLOBALS['config']['keep_log']) write_to_log($query);
    $querysplit = explode(' ',$query);
    switch($querysplit[0]) { // first word
      case 'get':
        $params = split("(get | of )",$query);
        if(count($params) == 2) { // get .
          return self::get($params[1]);
        } else { // get . of .
          return self::get($params[2],$params[1]);
        }
        break;
      
      case 'set':
        $params = split("(set | of | to )",$query);
        $objects = english_to_array($params[3]);
        if(count($objects) > 1 && is_plural($params[1])) $params[3] = $objects;
        return self::set($params[2],$params[1],$params[3]);
        break;
        
      case 'list':
        return self::_list(substr($query,11));
        break;
      
      case 'unset':
        $params = split("(unset | of )",$query);
        return self::_unset($params[2],$params[1]);
        break;
        
      case 'backlinks':
        return self::backlinks(substr($query,13));
        
      case 'describe':
        $split = split("(describe | as )",$query);
        return self::describe($split[1],$split[2]);
        
      case 'rename':
        $split = split("(rename | to )",$query);
        return self::rename($split[1],$split[2]);
        
      case 'between':
        $split = split("(between | and )",$query);
        return self::between($split[1],$split[2]);
    }
  }
  
  function get($subject,$predicate=NULL) {
    global $db;
    $subject_id = page::id_from_name($subject);
    if(is_null($predicate)) { // get .
      $result = $db->select('triples',array('subject_id'=>$subject_id));
      if($result) {
        // get names from id's, group plurals, put in result array
        $answers = array();
        foreach($result as $answer) {
          $predicate = page::name_from_id($answer['predicate_id']);
          $object = page::name_from_id($answer['object_id']);
          $answers[$predicate] = self::set_or_add($answers[$predicate],$object);
        }
        // pluralize key if value is an array
        foreach($answers as $predicate=>$object) {
          if(is_array($object)) {
            $answers[pluralize($predicate)] = $answers[$predicate];
            unset($answers[$predicate]);
          }
        }
        return $answers;
      } else return false;
    } else { // get . of .
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
  function set($subject,$predicate,$object) {
    if(is_plural($predicate)) { // set colors of the rainbow to red, orange, ...
      foreach($object as $item)
        triple::set($subject,singularize($predicate),$item,true);
      return true;
    } else { // set color of apple to red
      return triple::set($subject,$predicate,$object);
    }
  }
  function _list($criteria=NULL) {
    global $db;
    if(empty($criteria)) {
      return $db->select_column('pages','name');
    } else {
      $pairs = array_flatten(split("(\(|\)| and | or )",$criteria));
      foreach($pairs as $pair) {
        $split = split("( is not | isn't | is )",$pair);
        $pred = $split[0];
        $obj = $split[1];
        $pred_id = page::id_from_name($pred);
        $obj_id = page::id_from_name($obj);
        if(!$obj_id || !$pred_id) return false; // pages don't exist
        $sql = "(predicate_id = $pred_id AND object_id = $obj_id)";
        $criteria = str_replace($pair,$sql,$criteria);
      }
      // OCD
      $criteria = str_replace(' or ',' OR ',$criteria);
      $criteria = str_replace(' and ',' AND ',$criteria);
      // business
      $result = $db->select_column('triples','subject_id',$criteria);
      if($result) {
        $answers = array();
        foreach($result as $subject_id) {
          $answers[] = page::name_from_id($subject_id);
        }
        return $answers;
      } else {
        return false;
      }
    }
  }
  function _unset($subject,$predicate=NULL) {
    // FIXME: should unset delete the record in the pages table as well?
    if(is_null($predicate)) { // unset .
      $GLOBALS['db']->delete('triples',array(
        'subject_id' => page::id_from_name($subject)
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
  function backlinks($name) {
    // backlinks to .
    $matches = $GLOBALS['db']->select('triples',array(
      'object_id' => page::id_from_name($name)
    ));
    $answers = array();
    if($matches) {
      // get names from id's, group multiples
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
  function describe($name,$description=NULL) {
    global $db;
    if(is_null($description)) { // describe .
      write_to_log('describing '.$name);
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
  function rename($old_name,$new_name) {
    $GLOBALS['db']->update('pages',array('name'=>$new_name),array('name'=>$old_name));
    return true;
  }
  // FIXME: what if there are multiple predicates
  // between a given subject and object?
  function between($one,$two) {
    $ids = array(
      page::id_from_name($one),
      page::id_from_name($two)
    );
    // checks for links either direction
    $result = $GLOBALS['db']->select_one('triples','predicate_id',
      "(subject_id = $ids[0] AND object_id = $ids[1]) OR ".
      "(subject_id = $ids[1] AND object_id = $ids[0])");
    if($result) {return page::name_from_id($result);}else{return false;};
  }
  // just a helper...
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
