<?php
class BQL {
  function query($querystring) {
    echo $querystring."<br />";
    global $db;
    $query = BQL::split_but_not_in_quotes(trim($querystring));
    switch ($query[0]) { // first word of query
      case 'get':
        // get [predicate] of [subject]
        $params = array(
          'predicate_id' => page::id_from_name(self::resolve_quoted($query[1])),
          // [2] of
          'subject_id' => page::id_from_name(self::resolve_quoted($query[3]))
        );
        $answer = (int) $db->select_one('triples','object_id',$params);
        if(!$answer) {
          return false;
        } else {
          return page::name_from_id($answer);
        }
        break;
        
      case 'set':
        // set [predicate] of [subject] to [object]
        $predicate = self::resolve_quoted($query[1]);
        // [2] of
        $subject = self::resolve_quoted($query[3]);
        // [4] to
        $object = self::resolve_quoted($query[5]);
        $data = array(
          'predicate_id' => page::create_if_doesnt_exist($predicate),
          'subject_id' => page::create_if_doesnt_exist($subject),
          'object_id' => page::create_if_doesnt_exist($object),
        );
        // if this triple isn't already in the db, insert it
        if(!triple::exists($data['subject_id'],$data['predicate_id'],$data['object_id'])) {
          $db->insert('triples',$data);
        }
        return $object;
        break;
        
      case 'list':
        // list where [predicate] is [object], [predicate] is [object], ...
        $conditions_string = substr($querystring,11);
        $conditions = array();
        foreach(english_to_array($conditions_string) as $condition_string) {
          $condition = explode(' ',$condition_string);
          $pred_condition = 'predicate_id = '.page::id_from_name(self::resolve_quoted($condition[0]));
          // [1] is
          $obj_condition = 'object_id = '.page::id_from_name(self::resolve_quoted($condition[2]));
          $conditions[] = "($pred_condition AND $obj_condition)";
        }
        $matches = $db->select_column('triples','subject_id',implode(' OR ',$conditions));
        if($matches) {
          $answers = array();
          foreach($matches as $match) {
            $answers[] = page::name_from_id($match['subject_id']);
          }
          return $answers;
        }
        break;
      
      case 'unset':
        // unset [subject]
        $db->delete('triples',array(
          'subject_id' => page::id_from_name(self::resolve_quoted($query[1]))));
    }
  }
  // if a string has quotes around it, strip the quotes
  function resolve_quoted($string) {
    if(strpos($string,"'") == 0 && strrpos($string,"'") == strlen($string)-1) {
      return substr($string,1,strlen($string)-2);
    } else {
      return $string;
    }
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