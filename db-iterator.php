<?php
class DatabaseObjectIterator implements Countable, ArrayAccess, Iterator {
  function __construct($items) {
    $this->items = $items;
    $this->index = 0;  }
  /*
  function create($data=NULL) {
    eval("\$item = new $this->classname(\$data);");
    eval("\$item->{$this->parent_classname}_$this->parent_primary_key = $parent_primary_value;");
    return $item;
  }
  */
  // countable
  function count() {
    return count($this->items);
  }
  // arrayAccess
  function offsetExists($offset) {
    return isset($this->items[$this->index]) ? true : false;
  }
  function offsetGet($offset) {
    return $this->items[$offset];
  }
  function offsetSet($offset,$value) {
    throw new Exception("this is read-only, fool!");
  }
  function offsetUnset($offset) {
    throw new Exception("this is read-only, fool!");
  }
  // iterator
  function current() {
    return $this->items[$this->index];
  }
  function key() {
    return $this->index;
  }
  function next() {
    return $this->index ++;
  }
  function rewind() {
    return $this->index = 0;
  }
  function valid() {
    return $this->offsetExists($this->index);
  }
}
?>