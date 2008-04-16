<?php
class DatabaseObject {
  function __construct($primary_value=NULL,$tablename=NULL,$primary_key='id') {
    $tablename ? $this->tablename = $tablename : $this->tablename = pluralize(get_class($this));
    $this->primary_key = $primary_key; // for relationships, auto-incrementing
    $this->columns = $GLOBALS['db']->schema[$this->tablename];
    $this->has_many = array();
    $this->has_one = array();
    // fill with values from db
    if(!is_null($primary_value)) { // if created with a param
      $data = $GLOBALS['db']->select_row($this->tablename,array($this->primary_key=>$primary_value));
      if($data) { // record exists in db
        $this->fill($data);
        $this->in_db = true;
        $this->connect();
      } else {
        $this->in_db = false;
      }
    }
  }
  // move data in an associative array into the object itself
  function fill($array) {
    foreach($array as $column=>$value) {
      $this->$column = $value;
    }
    $this->in_db = true;
  }
  // move the object into an associative array
  function toArray() {
    $data = array();
    foreach($this->columns as $column) {
      $data[$column] = $this->$column;
    }
    return $data;
  }
  /** save/delete **/
  function save() {
    if(!$this->in_db) {
      // no record exists of this in the db
      $highkey = $GLOBALS['db']->select_one($this->tablename,$this->primary_key,'',"ORDER BY $this->primary_key DESC");
      $primary_key = $this->primary_key;
      if($highkey == false && $highkey != 0) {
        echo 'no record in db';
        $this->$primary_key = 0; // if no records in db, start at 0
      } else {
        $this->$primary_key = $highkey+1; // auto-increment
      }
      $GLOBALS['db']->insert($this->tablename,$this->toArray());
    } else {
      // this was already in the db
      $primary_key = $this->primary_key;
      $GLOBALS['db']->update($this->tablename,$this->toArray(),array($primary_key => $this->$primary_key));
    }
  }
  function delete() {
    if($this->in_db) {
      $primary_key = $this->primary_key;
      $GLOBALS['db']->delete($this->tablename,array($primary_key => $this->$primary_key));
    }
  }
  function delete_all() {
    foreach($this->has_many as $has_many=>$attribute) {
      if($this->$attribute) {
        foreach($this->$attribute as $item) {
          $item->delete();
        }
      }
    }
    $this->delete();
  }
  /** app helpers **/
  function exchange($from_key,$to_key,$value) { // ugghhh
    $item = $GLOBALS['db']->select_one(pluralize(get_class($this)),$to_key,array($from_key=>$value));
    if(!$item) {
      return NULL;
    } else {
      return $item;
    }
  }
  /** domain logic helpers **/
  function find($params='',$options='',$keep_going=true) {
    $items = array();
    $matches = $GLOBALS['db']->select($this->tablename,$params,$options);
    if(!$matches) { // no results
      return NULL;
    } else {
     foreach($matches as $match) {
        eval("\$item = new ".get_class($this)."();");
        $item->fill($match);
        if($keep_going) {
          $item->connect();
        }
        array_push($items,$item);
      }
      return $items;
    }
  }
  function find_all($options='',$keep_going=true) {
    $result = $this->find('',$options,$keep_going);
    if(!$result) {
      return NULL;
    } else {
      return $result;
    }
  }
  function find_one($params='',$options='',$keep_going=true) {
    $result = $this->find($params,$options,$keep_going);
    if($result != false) {
      return $result[0];
    } else {
      return NULL;
    }
  }
  function find_by($attribute,$value,$options='',$keep_going=true) {
    return $this->find(array($attribute=>$value),$options,$keep_going);
  }
  /** domain logic **/
  function has_many($class_name,$corresponding_key=NULL,$attribute_name=NULL) {
    if(is_null($corresponding_key)) $corresponding_key = get_class($this).'_'.$this->primary_key; // eg 'page_id'
    eval("\$that = new $class_name();");
    $primary_key = $this->primary_key;
    $tablename = $that->tablename;
    if(is_null($attribute_name)) $attribute_name = $tablename;
    $this->has_many[$class_name] = $attribute_name;
    $this->$attribute_name = $that->find(array($corresponding_key => $this->$primary_key),'',false);
  }
  function has_one($class_name,$corresponding_key=NULL,$attribute_name=NULL) {
    if(is_null($attribute_name)) $attribute_name = $class_name;
    $this->has_one[$class_name] = $attribute_name;
    eval("\$that = new $class_name();");
    if(is_null($corresponding_key)) $corresponding_key = $class_name.'_'.$that->primary_key;
    $this->$attribute_name = $that->find_one(array($that->primary_key => $this->$corresponding_key),'',false);
  }
  // TODO: belongs_to?
}
?>
