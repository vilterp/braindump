<?php
// FIXME: does it need the columns list?
class DatabaseObject {
  
  var $tablename;
  var $columns;
  var $primary_key;
  var $primary_value;
  var $in_db = false;
  var $data = array();
  var $dirty = array();
  var $has_one = array();
  var $has_many = array();
  var $has_many_through = array();
  
  function __construct($primary_value=NULL,$tablename=NULL,$primary_key='id') {
    is_null($tablename) ? 
      $this->tablename = pluralize(get_class($this)) : 
      $this->tablename = $tablename;
    // schema information
    $this->columns = $GLOBALS['db']->schema[$this->tablename];
    $this->primary_key = $primary_key;
    $this->primary_value = $primary_value;
    // load & register associations
    if(!is_null($primary_value)) $this->load($primary_value);
    if(method_exists($this,'connect')) $this->connect();
  }
  // load data from database into object
  function load($primary_value) {
    if(is_array($primary_value)) {
      $this->data = $primary_value;
      $this->primary_value = $this->data[$this->primary_key];
    } else {
      $this->data = $GLOBALS['db']->select_row($this->tablename,array($this->primary_key => $primary_value));
    }
   $this->in_db = true;
  }
  function save() {
    $data = array();
    foreach($this->dirty as $attr) {
      $data[$attr] = $this->data[$attr];
    }
    if($this->in_db) { // if a record is already there, just update it
      $GLOBALS['db']->update($this->tablename,$data,array($this->primary_key=>$this->primary_value));
    } else { // otherwise, insert a new record
      // find the highest primary key & increment it
      $highkey = $GLOBALS['db']->select_columns($this->tablename,$this->primary_key);
      if(!$highkey) $highkey = 0;
      $this->data[$this->primary_key] = ((int)$highkey)+1;
      // stick it in the db
      $GLOBALS['db']->insert($this->tablename,$this->data);
    }
  }
  function delete() {
    $GLOBALS['db']->delete($this->tablename,array($this->primary_key=>$this->primary_value));
  }
  function delete_all() {
    // TODO: delete_all...
  }
  
  function __set($attr,$value) {
    if(!is_null($this->columns) && in_array($attr,$this->columns)) { // if it's a column name
      $this->dirty[] = $attr; // keep track of changed (dirty) attributes
      $this->data[$attr] = $value;
    } else {
      $this->$attr = $value;
    }
  }
  function __get($attr) {
    // if it's a field loaded from the db
    if(!is_null($this->data) && array_key_exists($attr,$this->data)) {
      return $this->data[$attr];
    // if it's an associated object or list of objects
    } else if(array_key_exists($attr,$this->has_one)) {
      return $this->load_has_one($attr,$this->has_one[$attr]);
    } else if(array_key_exists($attr,$this->has_many)) {
      return $this->load_has_many($attr,$this->has_many[$attr]);
    } else if(array_key_exists($attr,$this->has_many_through)) {
      return $this->load_has_many_through($attr,$this->has_many_through[$attr]);
    }
  }
  function __call($name,$args) {
    // enables methods like "find_by_[attribute]([value],[options])"
    if(strpos($name,'find_by_') == 0) {
      $attr = substr($name,7);
      return $this->find(array($attr=>$args[0]),$args[1]);
    }
  }

  function find($params='',$options='') {
    $result = $GLOBALS['db']->select($this->tablename,$params,$options);
    $items = array();
    if(!$result) {
      return NULL;
    } else {
      foreach($result as $row) {
        $this_class = get_class($this);
        eval("\$item = new $this_class();");
        $item->load($row);
        $items[] = $item;
      }
      return $items;
    }
  }
  function find_one($params='',$options='') {
    $result = $this->find($params,$options);
    return $result[0];
  }
  function find_all($options='') {
    return $this->find('',$options);
  }
    
  function has_one($classname,$corresponding_key=NULL,$attribute=NULL) {
    // guess corresponding key, attribute if not supplied
    if(is_null($corresponding_key)) $corresponding_key = $classname.'_'.$this->primary_key;
    if(is_null($attribute)) $attribute = $classname;
    // put information in for later
    $this->has_one[$attribute] = array(
      'classname' => $classname,
      'corresponding_key' => $corresponding_key
    );
  }
  function load_has_one($attr,$values) {
    eval("\$that = new $values[classname]();");
    $corresponding_key = $values['corresponding_key'];
    $result = $that->find_one(array($that->primary_key=>$this->$corresponding_key));
    $this->$attr = $result;
    return $result;
  }

  function has_many($classname,$corresponding_key=NULL,$attribute=NULL) {
    // guess corresponding key, attribute if not supplied
    if(is_null($corresponding_key)) $corresponding_key = get_class($this).'_'.$this->primary_key;
    if(is_null($attribute)) $attribute = pluralize($classname);
    // put information in for later
    $this->has_many[$attribute] = array(
      'classname' => $classname,
      'corresponding_key' => $corresponding_key
    );
  }
  function load_has_many($attr,$values) {
    eval("\$that = new $values[classname]();");
    $result = $that->find(array($values['corresponding_key']=>$this->primary_value));
    $iterator = new DatabaseObjectIterator($result);
    $this->$attr = $iterator;
    return $iterator;
  }

  function has_many_through($classname,$tablename=NULL,$this_key=NULL,$that_key=NULL,$attribute=NULL) {
    // guess tablename, this_key, that_key, attribute if not supplied
    if(is_null($tablename)) $tablename = pluralize(get_class($this)).'_'.pluralize($classname);
    if(is_null($this_key)) $this_key = get_class($this).'_'.$this->primary_key;
    if(is_null($that_key)) $that_key = $classname.'_'.$this->primary_key;
    if(is_null($attribute)) $attribute = pluralize($classname);
    // put information in for later
    $this->has_many_through[$attribute] = array(
      'classname' => $classname,
      'tablename' => $tablename,
      'this_key' => $this_key,
      'that_key' => $that_key,
    );
  }
  function load_has_many_through($attr,$values) {
    $intermediate = $GLOBALS['db']->select(
      $values['tablename'],
      array($values['this_key']=>$this->primary_value)
    );
    $result = array();
    foreach($intermediate as $item) {
      eval("\$that = new $values[classname]();");
      $that->load($item[$values['that_key']]);
      $result[] = $that;
    }
    $this->$attr = $result;
    return $result;
  }
}
?>