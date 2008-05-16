<?php
// TODO: wrap these in functions
// FIXME: quoting..
class BQL {
  function query($query) {
    $querysplit = explode(' ',$query);
    switch($querysplit[0]) {
      case 'get':
        $params = split("(get | of )",$query);
        return self::_get($params[1],$params[2]);
        break;
      
      case 'set':
        $params = split("(set | of | to )",$query);
        return self::_set($params[1],$params[2],$params[3]);
        break;
        
      case 'list':
        return self::_list(substr($query,11));
        break;
      
      case 'unset':
        $params = split("(unset | of )",$query);
        return self::_unset($params[1],$params[2]);
        break;
    }
  }
  function _get($predicate,$subject) {
    if(is_null($subject)) { // get .
      $params = "subject_id = ".page::id_from_name($predicate);
      $result = $GLOBALS['db']->select('triples',$params);
      if($result) {
        $answer = array();
        foreach($result as $triple) {
          $answer[page::name_from_id($triple['predicate_id'])] = 
            page::name_from_id($triple['object_id']);
        }
        return $answer;
      } else {
        return false;
      };
    } else { // get . of .
      $params = array(
        'predicate_id' => page::id_from_name($predicate),
        'subject_id' => page::id_from_name($subject)
      );
      $answer = (int) $GLOBALS['db']->select_one('triples','object_id',$params);
      if($answer){return page::name_from_id($answer);} else {return false;}
    }
  }
  function _set($predicate,$subject,$object) {
    $data = array(
      'predicate_id' => page::create_if_doesnt_exist($predicate),
      'subject_id' => page::create_if_doesnt_exist($subject),
      'object_id' => page::create_if_doesnt_exist($object),
    );
    // if this triple isn't already in the db, insert it
    if(triple::exists($data['subject_id'],$data['predicate_id'])) {
      $GLOBALS['db']->update('triples',$data,array(
        'subject_id' => $data['subject_id'],
        'predicate_id' => $data['predicate_id']
      ));
    } else {
      $GLOBALS['db']->insert('triples',$data);
    }
    return true;
  }
  function _list($conditions_string) {
    // FIXME: this is really broken...
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
  function _unset($predicate,$subject) {
    $GLOBALS['db']->delete('triples',array(
      'predicate_id' => page::id_from_name($predicate),
      'subject_id' => page::id_from_name($subject)
    ));
    return true;
  }
  function split_but_not_in_quotes($string) {
    $quote_split = explode("'",$string);
    $in_quotes = false;
    $final = array();
    for($i=0; $i<count($quote_split); $i++) {
      if($in_quotes) {
        $final[] = $quote_split[$i];
        $in_quotes = false;
      } else {
        $split = explode(' ',$quote_split[$i]);
        foreach($split as $item) {
          if(!empty($item)) $final[] = $item;
        }
        $in_quotes = true;
      }
    }
    return $final;
  }
}
?>
