<?php
// TODO: override tablename, primary_key w/ 'var' declarations at beginning of class
class DatabaseObject {
  
  var $tablename;
  var $primary_key;
  var $primary_value;
  var $in_db = false;
  var $data = array();
  var $dirty = array();
  var $associations = array();
  var $autosave = false;
  
  function __construct($primary_value=NULL,$tablename=NULL,$primary_key='id') {
    is_null($tablename) ? 
      $this->tablename = pluralize(get_class($this)) : 
      $this->tablename = $tablename;
    // schema information
    $this->primary_key = $primary_key;
    $this->primary_value = $primary_value;
    // load & register associations
    if(!is_null($primary_value)) $this->load($primary_value);
    if(method_exists($this,'connect')) $this->connect();
  }
  function __destruct() {
    if($this->autosave) {
      $this->save();
    }
  }
  // load data from database into object
  function load($primary_value) {
    if(is_array($primary_value)) {
      $this->data = $primary_value;
      $this->primary_value = $this->data[$this->primary_key];
    } else {
      $result = $this->data = $GLOBALS['db']->select_row($this->tablename,array($this->primary_key => $primary_value));
      if($result) {
        $this->data = $result;
        $this->in_db = true;
      } else {
        $this->in_db = false;
      }
    }
  }
  function reload() {
    $this->load($this->primary_value);
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
      $highkey = $GLOBALS['db']->get_high_key($this->tablename,$this->primary_key);
      $this->data[$this->primary_key] = ((int)$highkey)+1;
      $this->primary_value = $this->data[$this->primary_key];
      // stick it in the db
      $GLOBALS['db']->insert($this->tablename,$this->data);
      $this->in_db = true;
    }
  }
  function delete() {
    $GLOBALS['db']->delete($this->tablename,array($this->primary_key=>$this->primary_value));
  }
  function delete_all() {
    // FIXME: redo this for new $this->associations format
    if($this->has_many) {
      foreach($this->has_many as $attr=>$values) {
        if($this->$attr) {
          foreach($this->$attr as $item) {
            $item->delete();
          }
        }
      }
    }
    if($this->has_many_through) {
      foreach($this->has_many_through as $attribute=>$value) {
        foreach($GLOBALS['db']->select(
          $value['tablename'],array($value['this_key']=>$this->primary_value)) as $item) {
            $GLOBALS['db']->delete($value['tablename'],$item);
          }
      }
    }
    $this->delete();
  }
  
  function __set($attr,$value) {
    $this->dirty[] = $attr; // keep track of changed (dirty) attributes
    $this->data[$attr] = $value;
  }
  function __get($attr) {
    if(array_key_exists($attr,$this->associations) && $this->in_db) {
      return eval("return \$this->load_".$this->associations[$attr]['type']."(\$attr,\$this->associations[\$attr]);");
    } else {
      return $this->data[$attr];
    }
  }
  function __call($name,$args) {
    if(!is_null($args[0])) {
      // enables methods like "find_by_[attribute]([value],[options])"
      if(strpos($name,'find_one_by_') !== false) {
        $attr = substr($name,12);
        return $this->find_one(array($attr=>$args[0]),$args[1]);
      } elseif(strpos($name,'find_by_') !== false) {
        $attr = substr($name,8);
        return $this->find(array($attr=>$args[0]),$args[1]);
      } else {
        throw new ErrorException("$name: no method by that name, pablo."); // FIXME: what's the right way to report errors...?
      }
    } else {
      return $this;
    }
  }

  function find($params='',$options='',$parent_classname=NULL) {
    $result = $GLOBALS['db']->select($this->tablename,$params,$options);
    $items = array();
    if(!$result) {
      return NULL;
    } else {
      foreach($result as $row) {
        $this_class = get_class($this);
        eval("\$item = new $this_class();");
        $item->load($row);
        $item->in_db = true;
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
  
  function add($attr) {
    $association = $this->associations[$attr];
    eval("\$item = new $association[classname]();");
    switch($association['type']) {
      case "belongs_to":
        $corresponding_key = $association['corresponding_key'];
        $item->$corresponding_key = $this->primary_value;
        break;
      case "has_many":
        $corresponding_key = $association['corresponding_key'];
        $item->$corresponding_key = $this->primary_value;
        break;
      default:
        $data = array(
          $association['this_key'] => $this->primary_value,
          // FIXME: do this without guessing...?
          $association['that_key'] => $GLOBALS['db']->get_high_key($item->tablename,$item->primary_key)+1
        );
        // FIXME: this shouldn't be automatic...
        $GLOBALS['db']->insert($association['tablename'],$data);
    }
    return $item;
  }
    
  function belongs_to($classname,$corresponding_key=NULL,$attribute=NULL) {
    // guess corresponding key, attribute if not supplied
    if(is_null($corresponding_key)) $corresponding_key = $classname.'_'.$this->primary_key;
    if(is_null($attribute)) $attribute = $classname;
    // put information in for later
    $this->associations[$attribute] = array(
      'type' => 'belongs_to',
      'classname' => $classname,
      'corresponding_key' => $corresponding_key
    );
  }
  function load_belongs_to($attr,$values) {
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
    $this->associations[$attribute] = array(
      'type' => 'has_many',
      'classname' => $classname,
      'corresponding_key' => $corresponding_key
    );
  }
  function load_has_many($attr,$values) {
    eval("\$that = new $values[classname]();");
    $result = $that->find(array($values['corresponding_key']=>$this->primary_value));
    $this->$attr = $result;
    return $result;
  }

  function has_many_through($classname,$tablename=NULL,$this_key=NULL,$that_key=NULL,$attribute=NULL) {
    // guess tablename, this_key, that_key, attribute if not supplied
    if(is_null($tablename)) $tablename = pluralize(get_class($this)).'_'.pluralize($classname);
    if(is_null($this_key)) $this_key = get_class($this).'_'.$this->primary_key;
    if(is_null($that_key)) $that_key = $classname.'_'.$this->primary_key;
    if(is_null($attribute)) $attribute = pluralize($classname);
    // put information in for later
    $this->associations[$attribute] = array(
      'type' => 'has_many_through',
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
  
  function belongs_to_many($classname,$tablename=NULL,$thiskey=NULL,$thatkey=NULL,$attribute=NULL) {
    // guess tablename, this_key, that_key, attribute if not supplied
    if(is_null($tablename)) $tablename = pluralize($classname).'_'.pluralize(get_class($this));
    if(is_null($this_key)) $this_key = get_class($this).'_'.$this->primary_key;
    if(is_null($that_key)) $that_key = $classname.'_'.$this->primary_key;
    if(is_null($attribute)) $attribute = pluralize($classname);
    // put information in for later
    $this->associations[$attribute] = array(
      'type' => 'belongs_to_many',
      'classname' => $classname,
      'tablename' => $tablename,
      'this_key' => $this_key,
      'that_key' => $that_key,
    );
  }
  function load_belongs_to_many($attr,$values) {
    return $this->load_has_many_through($attr,$values);
  }
}
?>